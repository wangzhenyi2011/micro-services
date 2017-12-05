<?php

namespace Lumen\Installer\Console;

use RuntimeException;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Micro-services VPGAME.')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('remote', InputArgument::REQUIRED);
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verifyApplicationDoesntExist(
            $directory = getcwd().'/'.$input->getArgument('name'),
            $output
        );


        $this->verifyApplicationDoesntExist(
             $directory = getcwd().'/lumen',
             $output
         );

        $remote = $input->getArgument('remote');
        $output->writeln('<info>Crafting application...</info>');
        exec('git clone '.$remote, $out, $renturn_val);

        if ($renturn_val === 0) {
            $output->writeln('<comment>Success of micro service cloning.</comment>');
        }

        $rename = rename(getcwd().'/lumen', $input->getArgument('name'));
        if($rename === true){
            $output->writeln('<comment>The name of the project is: '.$input->getArgument('name').'.</comment>');
        }
        chdir($input->getArgument('name'));
        exec("php -r \"copy('.env.tp', '.env');\"", $out, $res);
        if ($res ===0) {
            $output->writeln('<comment>Create a successful .env.</comment>');
        }

        exec('rm -rf '.getcwd().'/.git', $Out, $clear_res);
        if ($clear_res ===0) {
            $output->writeln('<comment>Clear .git file success</comment>');
        }

        $webcontent = file_get_contents('routes/web.php');
        $webcontent = sprintf($webcontent, $input->getArgument('name'));
        file_put_contents('routes/web.php', $webcontent);

        $appcontent = file_get_contents('bootstrap/app.php');
        $appcontent = sprintf($appcontent, $input->getArgument('name'));
        file_put_contents('bootstrap/app.php', $appcontent);

        exec("composer install", $out, $res);

        $output->writeln('<comment>Application ready! Build something amazing.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
    {
        if (is_dir($directory)) {
            throw new RuntimeException('Application already exists!');
        }
    }
}
