<?php

namespace App\Controller;

use Cake\Utility\Inflector;

class UploadfilesController extends AppController
{
    private $uploadPath = 'uploads' . DS . 'files' . DS;
    private $MAX_FILES_ALLOWED_PER_USER = 3;
    
    public function initialize(){
        parent::initialize();
        
        $this->loadComponent('Flash');
    }
    
    public function index() {
        //debug(Inflector::singularize('upload_files')); debug(Inflector::camelize(Inflector::singularize($this->request->params['controller'])));
        $existingFiles = $this->Uploadfiles->find('all')
                                   ->where(['Uploadfiles.user_id' => $this->Auth->user('id')])
                                   ->order(['Uploadfiles.created' => 'DESC'])->toArray();
        //debug($this->MAX_FILES_ALLOWED_PER_USER);
        if(count($existingFiles) > $this->MAX_FILES_ALLOWED_PER_USER) {
            $this->Flash->error(__('You have reached maximum permissible limit for uploading files. Please contact support.'));
            return null;
        }
        $newFile = (count($existingFiles) === 0) ? $this->Uploadfiles->newEntity() : $existingFiles[0];
        if ($this->request->is(['post', 'put'])) {
            if(!empty($this->request->data['file']['name']) && is_uploaded_file($this->request->data['file']['tmp_name'])){
                $fileName = $this->request->data['file']['name'];
                $uniqueId = uniqid();
                $generateFilename = WWW_ROOT . $this->uploadPath . DS . 'scorecard_' . $uniqueId . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                if(move_uploaded_file($this->request->data['file']['tmp_name'], $generateFilename)){
                    $newFile->name = 'scorecard_' . $uniqueId . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFile->path = $this->uploadPath;
                    $newFile->created = date("Y-m-d H:i:s");
                    $newFile->modified = date("Y-m-d H:i:s");
                    $newFile->user_id = $this->Auth->user('id');
                    if ($this->Uploadfiles->save($newFile)) {
                        $this->Flash->success(__('File has been uploaded successfully.'));
                    } else {
                        $this->Flash->error(__('Unable to upload file, please try again.'));
                    }
                } else {
                    $this->Flash->error(__('Unable to upload file, please try again.'));
                }
            } else {
                $this->Flash->error(__('Please choose a file to upload.'));
            }
        }
        
        $this->set('uploadData', $newFile);
        
        $filesRowNum = count($existingFiles);
        $this->set('files',$existingFiles);
        $this->set('filesRowNum',$filesRowNum);
    }
    
    public function isAuthorized($user = null) {
        // All users with role as 'exam' can add seats
        if (isset($user['role']) && $user['role'] === 'student' && ($this->request->getParam('action') === 'index')) {
            return true;
        }

        return parent::isAuthorized($user);
    }
}