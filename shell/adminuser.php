<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 28.06.16
 * Time: 13:38
 */
require_once 'abstract.php';

class Opensource_Shell_Adminuser extends Mage_Shell_Abstract
{
    const ERROR_MESSAGE = 'error';
    const IFORM_MESSAGE = 'information';
    const SUCCESS_MESSAGE = 'success';
    /**
     * Run script
     *
     */
    public function run()
    {
        ini_set('memory_limit','-1');
        if ($this->getArg('reset')) {

            $this->_showMessage(self::IFORM_MESSAGE,'for reset usser password you need input all data in field down');

            $userName = $this->_waitForInput('Input user name:');
            $newPassword = $this->_waitForInput('New password:');
            $salt = $this->_waitForInput('Salt for password:');

            $yesNoChange = $this->_waitForInput('You sure change password Y/N:');

            if (empty($userName)) {
                $this->_showMessage(self::ERROR_MESSAGE,'You dont input user name use `adminuser.php list` for get list all users');
                die();
            }

            if(empty($newPassword)) {
                $this->_showMessage(self::ERROR_MESSAGE,'You dont input password for user');
                die();
            }

            if(strtolower($yesNoChange) == 'y') {
                $this->_setNewPassword($userName,$newPassword,$salt);
            } else {
                $this->_showMessage(self::ERROR_MESSAGE,'You need type "y" for approve change');
                die();
            }

        } elseif ($this->getArg('list')) {
            $this->_listUsers();
        }
    }

    /**
     * set new password for user
     * @param $userName
     * @param $password
     */
    protected function _setNewPassword($userName,$password,$salt = null)
    {
        /** @var  Mage_Admin_Model_Resource_User_Collection $adminUsersCollection */
        $adminUsersCollection = Mage::getModel('admin/user')->getCollection();
        $adminUsersCollection
            ->addFieldToFilter('username',$userName)
            ->getFirstItem()
            ->load();
        $adminData = $adminUsersCollection->getData();
        $adminData = reset($adminData);

        /** @var Mage_Admin_Model_User $userModel */
        if(!isset($adminData['user_id'])) {
            $this->_showMessage(self::ERROR_MESSAGE,'This user not exist');
            die();
        }

        $userModel = Mage::getModel('admin/user')->load($adminData['user_id']);
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');
        try {
            $userModel->setPassword(
                $customer->hashPassword($password),
                $salt
            );
            $userModel->save();
        } catch (Exception $error) {
            $this->_showMessage(self::ERROR_MESSAGE,$error->getMessage());
        }
        $this->_showMessage(self::SUCCESS_MESSAGE,sprintf("Password for user: %s reset is successfull",$userName));
        die();
    }

    /**
     * show all admin username for reset password
     */
    protected function _listUsers()
    {
        /** @var  Mage_Admin_Model_Resource_User_Collection $adminUsersCollection */
        $adminUsersCollection = Mage::getModel('admin/user')->getCollection();
        /** @var Mage_Admin_Model_User $_user */
        foreach ($adminUsersCollection as $_user) {
            printf("\e[33m%s\e[0m:\e[32m%s\e[0m" . PHP_EOL, $_user->getUsername(), $_user->getName());
        }
    }

    /**
     * show message
     * @param $type
     * @param $userMessage
     */
    protected function _showMessage($type,$userMessage)
    {
        $message = '';
        if($type == self::ERROR_MESSAGE) {
            $message .= "\e[31m";
        } elseif ($type == self::IFORM_MESSAGE ) {
            $message .= "\e[33m";
        } elseif ($type == self::SUCCESS_MESSAGE) {
            $message .= "\e[32m";
        }

        $message .= $userMessage;
        $message .= "\e[0m";
        printf("%s".PHP_EOL,$message);
    }

    /**
     * wait input user for console
     * @param $message
     * @return string
     */
    protected function _waitForInput($message)
    {
        printf("\033[33m %s \033[32m ", $message);
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        echo "\e[0m";
        return trim($line);
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f adminuser.php -- [options]

  -h            Short alias for help
  help          This help
  reset         reset admin password
USAGE;
    }
}

$adminUser = new Opensource_Shell_Adminuser();
$adminUser->run();