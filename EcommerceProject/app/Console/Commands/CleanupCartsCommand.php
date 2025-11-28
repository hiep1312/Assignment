<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CleanupCartsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up expired or invalid carts and adjusts cart item quantities based on available product variant stock';

    /**
     * Execute the console command.
     */
    public function handle(CartRepositoryInterface $repository)
    {
        try {
            $isSuccess = $repository->refreshAndCleanupCarts(5, 'DAY');

            if($isSuccess){
                $this->info("✓ Cart cleanup completed successfully.");
                $this->line("  - Invalid cart items removed");
                $this->line("  - Cart item quantities adjusted");
                $this->line("  - Cart expirations extended by 5 days");
            }else {
                $this->error("✗ Cart cleanup failed to execute.");
                Log::warning("Cart cleanup task returned false - procedure may not have executed");
            }

            return Command::SUCCESS;

        }catch(QueryException $dbException) {
            Log::error("Cart cleanup task failed: {$dbException}");

            $this->error("✗ Cart cleanup failed due to database error.");
            $this->fail("An error occurred while cleaning up carts. Check the logs for details.");
        }
    }
}
