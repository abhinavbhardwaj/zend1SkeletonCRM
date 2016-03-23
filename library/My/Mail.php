<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
class My_Mail extends Zend_Mail {

    /**
     * 
     * @param string $charset
     */
    public function __construct($charset = NULL) {
        parent::__construct($charset);

        $this->setDefaultFrom(ADMIN_MAIL_ADDRESS, EMAIL_FROM_NAME);
        $this->setDefaultReplyTo(ADMIN_MAIL_ADDRESS, EMAIL_FROM_NAME);

        $this->addHeader('MIME-Version', '1.0');
        $this->addHeader('Content-type', 'text/html');
        $this->addHeader('charset', 'iso-8859-1');
        $this->addHeader('Content-Transfer-Encoding', '8bit');
        $this->addHeader('X-Mailer:', 'PHP/' . phpversion());

//        $config = array(
//            'ssl' => 'tls',
//            'port' => 587,
//            'auth' => 'login',
//            'username' => 'apps@myfinny.com',
//            'password' => '"7j~\j$<}Y<"2sq:',
//        );
//       
//        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
//        $this->setDefaultTransport($mailTransport);
    }

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param  Zend_Mail_Transport_Abstract $transport
     * @return Zend_Mail                    Provides fluent interface
     */
    public function send($transport = null) {

        try {
            return parent::send($transport);
        } catch (Zend_Mail_Exception $exc) {
            return FALSE;
            //echo $exc->getTraceAsString();
        }
    }

    /**
     * Sets the HTML body for the message
     *
     * @param  string    $html
     * @param  string    $charset
     * @param  string    $encoding
     * @return Zend_Mail Provides fluent interface
     */
    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
        parent::setBodyHtml($html, $charset, $encoding);
    }

}