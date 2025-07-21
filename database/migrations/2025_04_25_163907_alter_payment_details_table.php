<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_details',function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('invoice_id')->constrained('customers')->onDelete('cascade');
            $table->string('payee_name',100)->nullable()->after('current_paid_amount');
            $table->enum('payment_type',['check_payment','online_transaction','cash_payment','card_payment','mobile_banking'])
            ->default('cash_payment')->after('payee_name');
            $table->string('bank_name',50)->nullable()->after('payment_type');
            $table->string('bank_branch_name',50)->nullable()->after('bank_name');
            // check payment details
            $table->string('bank_account_number',50)->nullable()->after('bank_branch_name');
            $table->string('bank_cheque_number',60)->nullable()->after('bank_account_number');
            $table->string('bank_micr_code',60)->nullable()->after('bank_cheque_number');
            $table->date('bank_check_issue_date')->nullable()->after('bank_micr_code');
            $table->date('bank_check_cleared_at')->nullable()->after('bank_check_issue_date');
            $table->string('bank_cheque_image')->nullable()->after('bank_check_cleared_at');
            //online transaction details
            $table->string('sender_account_number',100)->nullable()->after('bank_cheque_image');
            $table->string('receiver_account_number',100)->nullable()->after('sender_account_number');
            $table->string('online_transaction_id',100)->nullable()->after('receiver_account_number');
            $table->enum('online_transfer_method',['iBanking', 'BEFTN', 'RTGS', 'Mobile App', 'ATM Transfer','NPSB', 'Other'])
            ->nullable()->after('online_transaction_id');
            
            // Card Details
            $table->string('card_number',50)->nullable()->after('online_transfer_method');
            $table->enum('card_type',['Visa', 'MasterCard', 'American Express', 'Discover', 'UnionPay', 'JCB', 'Diners Club'])
            ->nullable()->after('card_number');
            $table->string('last_four_digits',4)->nullable()->after('card_type');
            $table->string('card_expiry_date',50)->nullable()->after('last_four_digits');
            $table->string('card_cvv',50)->nullable()->after('card_expiry_date');
            $table->string('card_image')->nullable()->after('card_cvv');
            // Mobile Banking Details
            $table->string('mobile_banking_sender_number',50)->nullable()->after('card_image');
            $table->string('mobile_banking_receiver_number',50)->nullable()->after('mobile_banking_sender_number');
            $table->enum('mobile_banking_type', [
                'bKash',
                'Nagad',
                'Rocket',
                'Upay',
                'SureCash',
                'Tap',
                'mCash',
                'FirstCash',
                'UCash',
                'OK Wallet'
            ])->nullable()->after('mobile_banking_receiver_number');
            $table->string('mobile_banking_transaction_id',100)->nullable()->after('mobile_banking_type');
            $table->enum('mobile_banking_account_type',['Personal', 'Agent', 'Merchant'])->nullable()->after('mobile_banking_transaction_id');
            $table->string('mobile_banking_image')->nullable()->after('mobile_banking_account_type');

            $table->enum('status',['processing', 'paid', 'failed', 'refunded'])->default('success')->after('mobile_banking_image');
            $table->text('remarks')->nullable()->after('status');
            $table->string('received_by')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn([
                'customer_id',
                'payee_name',
                'payment_type',
                'bank_name',
                'bank_branch_name',
                'bank_account_number',
                'bank_cheque_number',
                'bank_micr_code',
                'bank_check_issue_date',
                'bank_check_cleared_at',
                'bank_cheque_image',
                'sender_account_number',
                'receiver_account_number',
                'online_transaction_id',
                'online_transfer_method',
                'status',
                'card_number',
                'card_type',
                'last_four_digits',
                'card_expiry_date',
                'card_cvv',
                'card_image',
                'mobile_banking_sender_number',
                'mobile_banking_receiver_number',
                'mobile_banking_type',
                'mobile_banking_transaction_id',
                'mobile_banking_account_type',
                'mobile_banking_image',
                'remarks',
                'received_by',
            ]);
        });
    }
};
