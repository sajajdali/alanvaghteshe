<?php

namespace Modules\Diet\app\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Package\Entities\PackageUser;
use Modules\Package\Enum\PackageUserTypeEnum;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DisableExpiredDietRequests extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diet:disable-expired';

    /**
     * The console command description.
     */
    protected $description = 'Disable users whose diet requests have expired';

    public function handle()
    {
        // package
        $userPackages = PackageUser::where('end_at', '<', Carbon::now())
            ->where('type', PackageUserTypeEnum::IN_USE)
            ->get();
        foreach ($userPackages as $userPackage) {
            $userPackage->type = PackageUserTypeEnum::END;
            $userPackage->save();
        }
        // diet
        $expiredDietRequests = DietRequest::where('end_date', '<', Carbon::now())
            ->where('active', 1)
            ->where('status', DietRequestStatusEnum::ACTIVE)
            ->get();

        foreach ($expiredDietRequests as $dietRequest) {
            $dietRequest->status = DietRequestStatusEnum::END;
            $dietRequest->save();
        }

        $this->info('Expired diet requests have been disabled.');
        return CommandAlias::SUCCESS;
    }
}
