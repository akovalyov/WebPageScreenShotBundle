<?php

/**
 * 
 * @author:  Baptiste BOUCHEREAU <baptiste.bouchereau@idci-consulting.fr>
 * @license: GPL
 *
 */

namespace IDCI\Bundle\WebPageScreenShotBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class CreateScreenshotCommand extends ContainerAwareCommand
{
    public static $PARAMS = array('url', 'width', 'height', 'mode', 'format');

    protected function configure()
    {
        $this
            ->setName('idci:create:screenshot')
            ->setDescription('Create (generate and resize) a screenshot from a website')
            ->setHelp(<<<EOT
            The <info>%command.name%</info> command create a screenshot from a website url.
            Do not forget the http:// as part of the url.
            You may add some options : width, height, mode(base64, file) and format (jpg, png, gif)
EOT
            )
            ->addArgument(
                    'url', InputArgument::REQUIRED, 'Which website do you want a screenshot from?'
            )
            ->addArgument(
                    'width', InputArgument::OPTIONAL, 'What will be the width of the screenshot?'
            )
            ->addArgument(
                    'height', InputArgument::OPTIONAL, 'What will be the height of the screenshot?'
            )
            ->addArgument(
                    'mode', InputArgument::OPTIONAL, 'Is this a file or a base64 encoded string?'
            )
            ->addArgument(
                    'format', InputArgument::OPTIONAL, 'What is the format of the image (png, jpg, gif)?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $this->getInputParams($input);
        $paramsNumber = count($params);

        if ($paramsNumber != 5 && $paramsNumber != 1) {
            $output->write("<error>You must indicate at least 5 parameters (width, height, mode and format).</error>");
            $output->writeln("<error>If none indicated, default ones can be set.</error>");
        } elseif ($paramsNumber == 5) {
            $screenshot = $this->getContainer()
                ->get('idci_web_page_screen_shot.manager')
                ->capture($params)
                ->resizeImage()
                ->getRenderer()
                ->render()
            ;
            $output->writeln($screenshot);
        } else {
            $output->writeln(array(
                'This command generate a website screenshot.',
                'In addition to the website url, you must indicate several options :',
                ' - <comment>width</comment>',
                ' - <comment>height</comment>',
                ' - <comment>mode</comment>',
                ' - <comment>format</comment>',
                ''
            ));

            $dialog = $this->getHelperSet()->get('dialog');
            $params = $this->askParams($dialog, $output, $params);
            $screenshot = $this->getContainer()
                ->get('idci_web_page_screen_shot.manager')
                ->capture($params)
                ->resizeImage()
                ->getRenderer()
                ->render()
            ;
            $output->writeln($screenshot);
        }
    }

    /**
     * Get parameters from the input
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return $params the given parameters
     */
    public function getInputParams(InputInterface $input)
    {
        $params = array();
        foreach (self::$PARAMS as $param) {
            if ($argument = $input->getArgument($param)) {
                $params[$param] = $argument;
            }
        }

        return $params;
    }

    /**
     * Ask user form parameters if none given (except the url which is required)
     * 
     * @param \Symfony\Component\Console\Helper\DialogHelper $dialog
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param type $params
     * @return $params the parameters array
     */
    public function askParams(DialogHelper $dialog, OutputInterface $output, $params)
    {
        $container = $this->getApplication()->getKernel()->getContainer();

        $defaultWidth = $container->getParameter('screenshot_width');
        $params['width'] = $dialog->ask(
                $output, sprintf('<info>Image width</info> [<comment>%s</comment>] : ', $defaultWidth), $defaultWidth
        );

        $defaultHeight = $container->getParameter('screenshot_height');
        $params['height'] = $dialog->ask(
                $output, sprintf('<info>Image height</info> [<comment>%s</comment>] : ', $defaultHeight), $defaultHeight
        );

        $defaultMode = $container->getParameter('screenshot_mode');
        $params['mode'] = $dialog->ask(
                $output, sprintf('<info>Image mode (base64, file)</info> [<comment>%s</comment>] : ', $defaultMode), $defaultMode
        );

        $defaultFormat = $container->getParameter('screenshot_format');
        $params['format'] = $dialog->ask(
                $output, sprintf('<info>Image format (png, jpg or jpeg, gif)</info> [<comment>%s</comment>] : ', $defaultFormat), $defaultFormat
        );

        return $params;
    }
}