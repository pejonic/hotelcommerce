<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminAboutHotelBlockSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_interior_image_to_move';
    public function __construct()
    {
        $this->table = 'htl_interior_image';
        $this->className = 'WkHotelInteriorImage';
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->context = Context::getContext();
        $this->identifier_name = 'display_name';

        $this->fields_options = array(
            'global' => array(
                'title' =>  $this->l('Hotel Interior Description'),
                'icon' =>   'icon-cogs',
                'fields' => array(
                    'HOTEL_INTERIOR_HEADING' => array(
                        'title' => $this->l('Interior Block Title'),
                        'type' => 'textLang',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a title for the interior block.')
                    ),
                    'HOTEL_INTERIOR_DESCRIPTION' => array(
                        'title' => $this->l('Interior Block Description'),
                        'type' => 'textareaLang',
                        'rows' => '4',
                        'cols' => '2',
                        'lang' => true,
                        'required' => true,
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Enter a description for the interior block.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'type' => 'submit',
                )
            ),
        );

        $this->fields_list = array(
            'id_interior_image' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Interior Image'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getInteriorImage',
                'class' => 'fixed-width-xs',
            ),
            'display_name' => array(
                'title' => $this->l('Display Name'),
                'align' => 'text-center',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-xs'
            ),
        );
        $this->identifier = 'id_interior_image';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );

        parent::__construct();
    }

    public function getInteriorImage($imgName)
    {
        if (file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$imgName)) {
            return '<img src="'._MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$imgName.
            '" class="img-thumbnail htlInteriorImg">';
        } else {
            return '--';
        }
    }

    public function renderList()
    {
        $this->informations[] = $this->l('For better view, upload hotel interior image in multiple of 3.');

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Hotel Image'),
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        $imageUrl = $imageSize = false;

        if ($this->display == 'edit') {
            $idHtlInterior = Tools::getValue('id_interior_image');
            $objHtlInteriorImg = new WkHotelInteriorImage($idHtlInterior);
            $imgName = $objHtlInteriorImg->name;

            $image = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$imgName;
            $imageUrl = ImageManager::thumbnail(
                $image,
                $this->table.'_'.(int)$idHtlInterior.'.'.$this->imageType,
                350,
                $this->imageType,
                true,
                true
            );
            $imageSize = file_exists($image) ? filesize($image) / 1000 : false;
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add New Hotel Interior Image'),
                'icon' => 'icon-list-ul'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Display name'),
                    'name' => 'display_name',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Hotel Interior Image'),
                    'name' => 'interior_img',
                    'required' => true,
                    'display_image' => true,
                    'image' => $imageUrl ? $imageUrl : false,
                    'size' => $imageSize,
                    'col' => 6,
                    'hint' => sprintf(
                        $this->l('Maximum image size: %1s'),
                        Tools::formatBytes(Tools::getMaxUploadSize())
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        $file = $_FILES['interior_img'];

        /*==== Validations ====*/
        if (Tools::getValue('display_name')) {
            if (!Validate::isCatalogName(Tools::getValue('display_name'))) {
                $this->errors[] = Tools::displayError($this->l('Please enter valid name.'));
            }
        }
        if (!(Tools::getValue("id_interior_image") && !$file['size'])) {
            if (!$file['size']) {
                $this->errors[] = Tools::displayError($this->l('Hotel Interior Image Required.'));
            } elseif ($file['error']) {
                $this->errors[] = Tools::displayError($this->l('Cannot upload file.'));
            } elseif (!(preg_match('/\.(jpe?g|gif|png)$/', $file['name'])
                && ImageManager::isRealImage($file['tmp_name'], $file['type']))
            ) {
                $this->errors[] = Tools::displayError($this->l('Please upload image file.'));
            }
        }

        /*==== Validations ====*/
        if (!count($this->errors)) {
            if (Tools::getValue("id_interior_image")) {
                $objHtlInteriorImg = new WkHotelInteriorImage(Tools::getValue("id_interior_image"));
            } else {
                $objHtlInteriorImg = new WkHotelInteriorImage();
                $objHtlInteriorImg->position = WkHotelInteriorImage::getHigherPosition();
            }

            if (Tools::getValue("id_interior_image") && $file['size'] && !$file['error']) {
                unlink(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$objHtlInteriorImg->name);
            }

            if ($file['size']) {
                do {
                    $tmp_name = uniqid().'.jpg';
                } while (file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$tmp_name));
                ImageManager::resize(
                    $file['tmp_name'],
                    _PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$tmp_name
                );

                $objHtlInteriorImg->name = $tmp_name;
            }

            $objHtlInteriorImg->display_name = Tools::getValue('display_name');
            $objHtlInteriorImg->active = Tools::getValue('active');
            $objHtlInteriorImg->save();

            if (Tools::getValue("id_interior_image")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if (Tools::getValue("id_interior_image")) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptions'.$this->table)) {
            // check if field is atleast in default language. Not available in default prestashop
            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $languages = Language::getLanguages(false);
            if (!trim(Tools::getValue('HOTEL_INTERIOR_HEADING_'.$defaultLangId))) {
                $this->errors[] = $this->l('Interior block title is required at least in ').
                $objDefaultLanguage['name'];
            }
            if (!trim(Tools::getValue('HOTEL_INTERIOR_DESCRIPTION_'.$defaultLangId))) {
                $this->errors[] = $this->l('Interior block description is required at least in ').
                $objDefaultLanguage['name'];
            }
            if (!count($this->errors)) {
                foreach ($languages as $lang) {
                    // if lang fileds are at least in default language and not available in other languages then
                    // set empty fields value to default language value
                    if (!trim(Tools::getValue('HOTEL_INTERIOR_HEADING_'.$lang['id_lang']))) {
                        $_POST['HOTEL_INTERIOR_HEADING_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_INTERIOR_HEADING_'.$defaultLangId
                        );
                    }
                    if (!trim(Tools::getValue('HOTEL_INTERIOR_DESCRIPTION_'.$lang['id_lang']))) {
                        $_POST['HOTEL_INTERIOR_DESCRIPTION_'.$lang['id_lang']] = Tools::getValue(
                            'HOTEL_INTERIOR_DESCRIPTION_'.$defaultLangId
                        );
                    }
                }
                // if no custom errors the send to parent::postProcess() for further process
                parent::postProcess();
            }
        } else {
            parent::postProcess();
        }
    }

    // update positions
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idInteriorImage = (int) Tools::getValue('id');
        $positions = Tools::getValue('interior_image');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idInteriorImage) {
                if ($objInteriorImg = new WkHotelInteriorImage((int) $pos[2])) {
                    if (isset($position)
                        && $objInteriorImg->updatePosition($way, $position, $idInteriorImage)
                    ) {
                        echo 'ok position '.(int) $position.' for testimonial block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update testimonial block position '.
                        (int) $idInteriorImage.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This testimonial block ('.(int) $idInteriorImage.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'wkabouthotelblock/views/css/WkAboutHotelBlockAdmin.css');
    }
}
