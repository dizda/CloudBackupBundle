<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;


/**
 * Class BaseProcessorTest.
 */
class BaseProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildArchiveFilename()
    {
        //This tetst is invalid. We should not test an abstract class
        /*
        $rootPath = '/';
        $outputPath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';

        // use tar processor, but call only BaseProcessor methods
        $processor = self::$kernel->getContainer()->get('dizda.cloudbackup.processor.tar');
        $processor->__construct($rootPath, $outputPath, 'database', array(),
            array(
                'date_format' => $dateformat,
                'options'     => array()
            )
        );

        $this->assertEquals(
            $processor->buildArchiveFilename(),
            'database_'.date($dateformat).'.tar');
         * **/
    }
}
