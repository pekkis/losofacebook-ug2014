<?php

namespace Losofacebook\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Losofacebook\Service\AsseticService;
use Assetic\Asset\GlobAsset;

class AssetizeCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('deploy:assetize')
            ->addArgument('version', InputArgument::REQUIRED)
            ->setDescription('Assetizes assets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $projectDir = realpath($this->getProjectDirectory());

        $options = array(
            'javaPath' => '/usr/bin/java',
            'closureCompilerPath' => $projectDir . "/app/dev/compiler.jar",
            'nodePath' => '/opt/local/bin/node',
            'nodePaths' => array($projectDir . '/node_modules'),
            'optiPngPath' => '/opt/local/bin/optipng',
            'jpegOptimPath' => '/opt/local/bin/jpegoptim',

            'collections' => array(

                'essentialjs' => array(
                    'write' => array('combined' => true, 'leaves' => false),
                    'cache' => false,
                    'options' => array(
                        'debug' => false,
                        'name' => 'essential',
                        'output' => "assets/{$version}/*",
                    ),
                    'filters' => '?closure',

                    'inputs' => array(
                        $projectDir . '/web/js/app.js',
                        $projectDir . '/web/js/controllers.js',
                        $projectDir . '/web/js/directives.js',
                        $projectDir . '/web/js/services.js',
                        $projectDir . '/web/js/filters.js',
                    )
                ),

                'css' => array(
                    'write' => array('combined' => true, 'leaves' => false),
                    'cache' => false,
                    'options' => array(
                        'debug' => false,
                        'name' => 'common',
                        'output' => "assets/{$version}/*.css",
                    ),
                    'filters' => 'less',
                    'inputs' => array(
                        $projectDir . '/web/css/bootstrap.css',
                        $projectDir . '/web/css/bootstrap-responsive.css',
                        $projectDir . '/web/css/losofacebook-main.less',
                    )
                )


            ),

            /*
            'parser' => array(
                'lus' => array(
                    'debug' => false,
                    'directory' => APPLICATION_PATH . '/assets',
                    'blacklist' => array(),
                    'files' => array(
                        'jpg' => array(
                            'pattern' => "/\.jpg$/",
                            'filters' => array('?jpegoptim'),
                            'output' => 'assets/*.jpg',
                        ),
                        'png' => array(
                            'pattern' => "/\.png$/",
                            'filters' => array('?optipng'),
                            'output' => 'assets/*.png',
                        ),
                        'ttf' => array(
                            'pattern' => "/\.ttf$/",
                            'filters' => array(),
                            'output' => 'assets/*.ttf',
                        ),


                    ),

                )

            ),
             */
        );

        $asseticService = new AsseticService($projectDir, $options);
        $asseticService->init();


    }
}



