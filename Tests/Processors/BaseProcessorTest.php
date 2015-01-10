<?php

namespace Dizda\CloudBackupBundle\Tests\Processors;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class BaseProcessorTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class BaseProcessorTest extends AbstractTesting
{
    public function testBuildArchiveFilename()
    {
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
    }

}
