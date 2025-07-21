<?php
declare(strict_types=1);

namespace App\Controller;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use App\Controller\AppController;

/**
 * Leavetypes Controller
 *
 * @property \App\Model\Table\LeavetypesTable $Leavetypes
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * /**
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */

class LeavetypesController extends AppController
{
	public function initialize(): void
	{
		parent::initialize();

		$this->loadComponent('Search.Search', [
			'actions' => ['index'],
		]);
	}
	
	public function beforeFilter(\Cake\Event\EventInterface $event)
	{
		parent::beforeFilter($event);
	}

	/*public function viewClasses(): array
    {
        return [JsonView::class];
		return [JsonView::class, XmlView::class];
    }*/
	
	public function json()
    {
		$this->viewBuilder()->setLayout('json');
        $this->set('leavetypes', $this->paginate());
        $this->viewBuilder()->setOption('serialize', 'leavetypes');
    }
	
	public function csv()
	{
		$this->response = $this->response->withDownload('leavetypes.csv');
		$leavetypes = $this->Leavetypes->find();
		$_serialize = 'leavetypes';

		$this->viewBuilder()->setClassName('CsvView.Csv');
		$this->set(compact('leavetypes', '_serialize'));
	}
	
	public function pdfList()
	{
		$this->viewBuilder()->enableAutoLayout(false); 
		$leavetypes = $this->paginate($this->Leavetypes);
		$this->viewBuilder()->setClassName('CakePdf.Pdf');
		$this->viewBuilder()->setOption(
			'pdfConfig',
			[
				'orientation' => 'portrait',
				'download' => true, 
				'filename' => 'leavetypes_List.pdf' 
			]
		);
		$this->set(compact('leavetypes'));
	}
   
    /**
     * View method
     *
     * @param string|null $id Leavetype id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', 'Leavetypes Details');
        $leavetype = $this->Leavetypes->get($id, contain: []);
        $this->set(compact('leavetype'));

        $this->set(compact('leavetype'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', 'New Leavetypes');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Add']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Leavetypes']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity()->slug]);
			}
		});
        $leavetype = $this->Leavetypes->newEmptyEntity();
        if ($this->request->is('post')) {
            $leavetype = $this->Leavetypes->patchEntity($leavetype, $this->request->getData());
            if ($this->Leavetypes->save($leavetype)) {
                $this->Flash->success(__('The leavetype has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The leavetype could not be saved. Please, try again.'));
        }
        $this->set(compact('leavetype'));
    }

}
