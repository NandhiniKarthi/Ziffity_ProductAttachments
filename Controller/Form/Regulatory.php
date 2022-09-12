<?php
namespace Ziffity\ProductAttachments\Controller\Form;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Ziffity\ProductAttachments\Helper\Email;
use Ziffity\ProductAttachments\Model\FileFactory;

class Regulatory extends Action
{
    protected $messageManager;
    protected $helper;
    protected $resultJsonFactory;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        TransportBuilder $transportBuilder,
        FileFactory $fileFactory,
        JsonFactory $resultJsonFactory,
        Email $helper
    ) {
        $this->messageManager = $messageManager;
        $this->_fileFactory = $fileFactory;
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPost();

        if ($data) {
            $model = $this->_fileFactory->create();
            $model->load($data['document']);

            $attachmentData = $model->getFile();
            $emailTemplateVariables['subject'] = 'Regulatory Document';

            $receiver = ['name' => 'Customer','email' => $data['email']];
            try {
                $this->helper->sendEmail($emailTemplateVariables, $receiver, $attachmentData);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }

        return  $result->setData(['status'=> true, 'message' => "Email Sent Successfully"]);
    }
}
