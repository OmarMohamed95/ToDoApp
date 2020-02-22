<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\MailerService;
use App\Repository\UserRepository;

class TasksReportCommand extends Command
{
    protected static $defaultName = 'app:tasks-report';
    private $mailerService;
    private $userRepo;

    public function __construct(MailerService $mailerService, UserRepository $userRepo)
    {
        $this->mailerService = $mailerService;
        $this->userRepo = $userRepo;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Send tasks report email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Sending the report...');
        
        $users = $this->GetUsersWeeklyInfo();

        foreach($users as $key => $user) {
            $this->sendEmail($user);
            
            $output->writeln($k + 1 . ' of ' . count($users) . ' has been sent.');
        }
        
        $output->writeln('Done.');

        return 0;
    }

    private function sendEmail(array $info)
    {
        $data['from'] = 'todoapp@example.com';
        $data['to'] = $info['email'];
        $data['subject'] = 'Tasks Weekly Report';
        $data['text'] = sprintf('You have created %s tasks this week, %s of them have been done.', $info['tasks_count'], $info['done_tasks']);

        $this->mailerService->setData($data);
        $this->mailerService->sendEmail();
    }

    private function GetUsersWeeklyInfo()
    {
        return $this->userRepo->GetUsersWeeklyInfo();
    }
}
