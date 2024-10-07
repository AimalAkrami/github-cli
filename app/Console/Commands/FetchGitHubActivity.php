<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class FetchGitHubActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:activity {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and display recent GitHub Activity for a user';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');

        $url = "https://api.github.com/users/{$username}/events";

        $client = new Client();

        try {

            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'Laravel CLI',
                ],
            ]);

            $events = json_decode($response->getBody(), true);

            if (empty($events)) {
                $this->info("No recent activity found for user {$username}");
                return 0;
            }

            foreach ($events as $event) {
                $type = $event['type'];
                $repo = $event['repo']['name'];
                $created_at = $event['created_at'];

                $this->info("Event Type: {$type}");
                $this->info("Repository: {$repo}");
                $this->info("Created At: {$created_at}");
                $this->info(str_repeat('_', 40));
            }
            return 0;
        } catch (\Exception $exception) {
            $this->error('Error fetching activity: ' . $exception->getMessage());
            return 1;
        }
    }
}
