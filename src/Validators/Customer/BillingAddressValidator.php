<?php

namespace IO\Validators\Customer;

use Plenty\Validation\Validator;
use IO\Services\TemplateConfigService;

class BillingAddressValidator extends Validator
{
    private $requiredFields;
    private $shownFields;

    public static $addressData;
    
    public function defineAttributes()
    {
        /**
         * @var TemplateConfigService $templateConfigService
         */
        $templateConfigService = pluginApp(TemplateConfigService::class);
        $requiredFieldsString  = $templateConfigService->get('billing_address.require');
        $this->requiredFields  = explode(', ', $requiredFieldsString);
        $shownFieldsString     = $templateConfigService->get('billing_address.show');
        $this->shownFields     = explode(', ', $shownFieldsString);
        foreach ($this->requiredFields as $key => $value)
        {
            $this->requiredFields[$key] = str_replace('billing_address.', '', $value);
        }

        foreach ($this->shownFields as $key => $value)
        {
            $this->shownFields[$key] = str_replace('billing_address.', '', $value);
        }
        
        $this->addString('name2',      true);
        $this->addString('name3',      true);
        $this->addString('address1',   true);
        $this->addString('address2',   true);
        $this->addString('postalCode', true);
        $this->addString('town',       true);
        
        if(count($this->requiredFields))
        {
            if(empty(self::$addressData['gender']))
            {
                $this->addString('name1',     $this->isRequired('name1'));
                $this->addString('vatNumber', $this->isRequired('vatNumber'));
            }

            $this->addString('birthday',  $this->isRequired('birthday'));
            $this->addString('name4',     $this->isRequired('name4'));
            $this->addString('address3',  $this->isRequired('address3'));
            $this->addString('address4',  $this->isRequired('address4'));
            $this->addString('stateId',  $this->isRequired('stateId'));
            $this->addString('title', $this->isRequired('title'));
            $this->addString('telephone', $this->isRequired('telephone'));
        }
    }
    
    private function isRequired($fieldName)
    {
        return in_array($fieldName, $this->shownFields) && in_array($fieldName, $this->requiredFields);
    }
}