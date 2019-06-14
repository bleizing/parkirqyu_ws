<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

use App\User;
use App\Employee;
use App\Vehicle;
use App\Balance;
use App\Invoice;
use App\Transaction;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$user_admin = User::create([
    		'email' => 'dimasz_97@yahoo.com',
    		'password' => Hash::make('test123'),
    		'user_type' => 1
    	]);

    	$employee_admin = Employee::create([
    		'user_id' => $user_admin->id,
    		'nama' => 'Dimas Admin',
    		'jenis_kelamin' => 1,
    		'tempat_lahir' => 'Jakarta',
    		'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', '01/01/1990'),
    		'alamat' => 'kalbis'
    	]);

    	$user = User::create([
    		'email' => 'dimas@bleizing.com',
    		'password' => Hash::make('test123'),
    		'user_type' => 3
    	]);

    	$employee = Employee::create([
    		'user_id' => $user->id,
    		'nama' => 'Dimas',
    		'jenis_kelamin' => 1,
    		'tempat_lahir' => 'Jakarta',
    		'tanggal_lahir' => Carbon::createFromFormat('d/m/Y', '01/01/1990'),
    		'alamat' => 'kalbis'
    	]);

    	$vehicle = Vehicle::create([
    		'user_id' => $user->id,
    		'nomor_registrasi' => 'B1234ABC',
    		'nama_pemilik' => 'Dimas',
    		'alamat' => 'Bekasi',
    		'merk' => 'Honda',
    		'type' => 'ABCDEF8G A/T',
    		'tahun_pembuatan' => '2010',
    		'nomor_rangka' => 'ABCDEFGHIJKLMNOPQ',
    		'nomor_mesin' => 'ABCEFGHIJKL',
            'vehicle_type' => 1
    	]);

    	$balance = Balance::create([
    		'user_id' => $user->id,
    		'nominal' => 100000
    	]);

        $balance_admin = Balance::create([
            'user_id' => $user_admin->id,
            'nominal' => 1000000
        ]);

    	$invoice_parkir = Invoice::create([
    		'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'invoice_code' => 'T6390582563',
    		'invoice_type' => 1
    	]);

        $invoice_topup = Invoice::create([
            'user_id' => $user->id,
            'invoice_code' => 'T6390582563',
            'invoice_type' => 2,
            'reference_code' => 'ABCD123EFG',
            'nominal' => 50000
        ]);

        $invoice_parkir->nominal = 10000;
        $invoice_parkir->save();

    	$transaction_parkir = Transaction::create([
    		'invoice_id' => $invoice_parkir->id,
            'nominal_kredit' => $invoice_parkir->nominal,
    		'petugas_id' => $user_admin->id
    	]);

        $invoice_parkir->is_active = 2;
        $invoice_parkir->save();

        $transaction_topup = Transaction::create([
            'invoice_id' => $invoice_topup->id,
            'nominal_debit' => $invoice_topup->nominal
        ]);

    	$invoice_topup->is_active = 2;
    	$invoice_topup->save();
    }
}
