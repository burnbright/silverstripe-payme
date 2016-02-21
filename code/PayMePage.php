<?php

class PayMePage extends Page
{

    private static $db = array(
        "Currency" => "Varchar",
        "SuccessTitle" => "Varchar",
        "SuccessContent" => "HTMLText"
    );

    private static $has_many = array(
        "Invoices" => "PaymentPage_Invoice"
    );

    private static $defaults = array(
        "Currency" => "NZD"
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab("Root.SuccessContent", array(
            TextField::create("Currency"),
            TextField::create("SuccessTitle"),
            HTMLEditorField::create("SuccessContent")
        ));
        $fields->addFieldToTab("Root.Invoices",
            GridField::create("Invoices", "Invoices", $this->Invoices(), GridFieldConfig_RecordEditor::create())
        );
        return $fields;
    }
}

class PayMePage_Controller extends Page_Controller
{

    private static $allowed_actions = array('Form','complete');

    public function Form()
    {
        return new PaymentForm($this, "Form",
            new FieldList(
                CurrencyField::create("Amount", "Amount", 20),
                DropdownField::create("Gateway", "Gateway", Payment::get_supported_gateways())
            ), new FieldList(
                FormAction::create("submit")
            )
        );
    }

    public function submit($data, $form)
    {
        $invoice = new PaymentPage_Invoice();
        $invoice->write();
        
        $form->saveInto($invoice);
        
        $payment = Payment::create()
            ->init($data['Gateway'], $invoice->Amount, $this->Currency)
            ->setReturnUrl($this->Link('complete')."?invoice=".$invoice->ID)
            ->setCancelUrl($this->Link()."?message=payment cancelled");
        $payment->write();

        $invoice->ParentID = $this->ID;
        $invoice->PaymentID = $payment->ID;
        $invoice->write();
        
        $payment->purchase($form->getData())
            ->redirect();
    }

    public function complete()
    {
        return array(
            'Title' => $this->SuccessTitle,
            'Content' => $this->SuccessContent,
            'Form' => '',
            'Invoice' => PayMePage_Invoice::get()->byID($this->request->getVar('invoice'))
        );
    }
}

class PayMePage_Invoice extends DataObject
{

    public static $db = array(
        "Amount" => "Currency"
    );

    public static $has_one = array(
        "Parent" => "PaymentPage",
        "Payment" => "Payment"
    );

    public static $summary_fields = array(
        "Amount", "Created"
    );
}
