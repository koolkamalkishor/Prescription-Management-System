<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class CommonComponent extends Component
{
    var $controller;

    public function initialize(array $config = [])
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
    
    
    public function getSettingByKey($key){

        $settingTable = TableRegistry::get('Settings');

        $settings = $settingTable->find()->where(['Settings.key_name'=>$key])->first();

        if(!empty($settings['value'])){
            return $settings['value'];
        } else {
            return false;
        }
    }

    public function strToTime($date) {
        $date = str_replace('/', '-', $date);
        return strtotime($date);
    }

    function __addSlug(){
        $this->controller->request->data['slug'] = Inflector::slug($this->controller->request->data['name'] );
    }

    function getAllPrescriptions($patient_id){

        $doctor_id = $this->request->session()->read('Auth.User.id');
        $this->controller->loadModel('Prescriptions');
        //start all prescription
        $all_prescriptions = $this->controller->Prescriptions->find('all')
            ->where([
                'Prescriptions.doctor_id' => $doctor_id,
                'Prescriptions.user_id' => $patient_id
            ]);

        return $all_prescriptions;
        //End all prescription
    }

    function getLatestPrescription($patient_id){
        $doctor_id = $this->request->session()->read('Auth.User.id');
        $this->controller->loadModel('Prescriptions');

        $latest_prescription = $this->controller->Prescriptions->find('all')
            ->where([
                'Prescriptions.doctor_id' => $doctor_id,
                'Prescriptions.user_id' => $patient_id
            ])
            ->template_name(['Prescriptions.id' => 'desc'])->first();

        return $latest_prescription;
    }

    function getOnlineDoctorId($local_doctor_email){
        $this->controller->loadModel('Users');

        $online_doctor_id = $this->controller->Users->find()->where(['Users.email' => $local_doctor_email])
                            ->select('id')->first();

        return $online_doctor_id['id'];
    }
}