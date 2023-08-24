<?php
/**
 * Main class of module mjslidervideo
 * @author Michał Jendraszczyk
 * @copyright (c) 2023, MAGES Michał Jendraszczyk
 * @license http://mages.pl mages.pl
 */

class Mjslidervideo extends Module
{

    
    public function __construct()
    {
        $this->name = 'mjslidervideo';

        $this->version = '1.0.0';
        $this->tab = 'front_office_features';

        $this->author = 'Michał Jendraszczyk';

        $this->ps_version = Tools::substr(_PS_VERSION_, 0, 3);

        $this->displayName = $this->l('Slider wideo');
        $this->description = $this->l('Pozwala ustawić slider w formie wideo');

         
        $this->bootstrap = true;
        parent::__construct();
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader');
    }

    public function hookDisplayHeader($params) { 
        $this->context->controller->addCss($this->_path . '/views/css/mjslidervideo.css');
    }
    public function uninstall()
    {
        return parent::uninstall();
    }
    
    public function getContent() { 

        if(!empty(Configuration::get('filename_slider_video'))) { 
        $_html = '
        <div class="home_mjslidervideo panel">
        <div class="panel-heading">
        Podgląd wideo
        </div>
        <div class="panel-body">
         
<video autoplay="autoplay" loop="loop" muted defaultMuted playsinline id="myVideo">
  <source src="'.(Tools::usingSecureMode() ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.Configuration::get('filename_slider_video').'" type="video/mp4">
</video>
</div>
</div>
        ';
        } else { 
            $_html =  '';  
        }
        return $this->postProcess().$this->renderForm().$_html;
    }

    public function postProcess() { 
        if(Tools::isSubmit('submitAddconfiguration')) { 
            
            // Jesli wgrywamy plik
            if(!empty($_FILES['filename_slider_video'])) {
               
                //  echo "A";
                // print_r($_POST);
                // print_r($_FILES);
                // echo "<Br/>>";
                // print_r($target_dir);
                // echo "<Br/>>";
                // print_r($target_file);
                // exit();

                $type = $_FILES["filename_slider_video"]["type"];
                $pos_start = strpos($type,"/");
                $ext = substr($type,$pos_start+1);

                $target_dir = _PS_MODULE_DIR_."/".$this->name."/uploads/";
                $target_file = $target_dir . md5(basename($_FILES["filename_slider_video"]["name"])).".".$ext;

                $allow_ext = ['mp4','webm','ogg','ogv'];
                             
                if(in_array($ext, $allow_ext)) { 

                if (move_uploaded_file($_FILES["filename_slider_video"]["tmp_name"], $target_file)) {
                    Configuration::updateValue('filename_slider_video',$target_file);
                  } else {

                }
            } else { 

            }
            
            } 

            // Jesli tylko aktualizujemy konfiguracje
            Configuration::updateValue('enable_slider_video',Tools::getValue('enable_slider_video'));
            
        }
    }
    
    public function renderForm() { 

        $form_fields = [];

        $form_fields[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Ustawienia')
            ),
            'input' => array(
                array(
                    'label' => $this->l('Włącz slider wideo'),
                    'type' => 'switch',
                    'name' => 'enable_slider_video',
                    'class' => 'form-control',
                    'values' => array(
                        [
                            'id' => 'enable_slider_video_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'enable_slider_video_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    )
                ),
                array(
                    'label' => $this->l('Slider wideo'),
                    'type' => 'file',
                    'name' => 'filename_slider_video',
                    'class' => 'form-control'
                )
            ),
            'submit' => array(
                'title' => 'Zapisz',
                'class' => 'btn btn-default pull-right'
            )
        );
        $form = new HelperForm();

        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->default_form_language = $this->context->language->id;
        $form->languages = Language::getLanguages();

        $form->tpl_vars['fields_value']['enable_slider_video'] = Tools::getValue('enable_slider_video', Configuration::get('enable_slider_video'));
        $form->tpl_vars['fields_value']['filename_slider_video'] = Tools::getValue('filename_slider_video', Configuration::get('filename_slider_video'));
    

        return $form->generateForm($form_fields);
    }
   
    public function hookDisplayHome()
    {
        $this->context->smarty->assign([
            'slider_video' => (Tools::usingSecureMode() ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.Configuration::get('filename_slider_video')
            ]
        );
        return $this->fetch('module:' . $this->name . '/views/templates/hook/home.tpl');
    }
}
