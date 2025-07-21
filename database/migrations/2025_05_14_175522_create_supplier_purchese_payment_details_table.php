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
        Schema::create('supplier_purchese_payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('purchase_id');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->double('current_paid_amount',15,2);
            $table->string('payee_name', 100)->nullable();
            $table->enum('payment_type', ['check_payment', 'online_transaction', 'cash_payment', 'card_payment', 'mobile_banking'])->default('cash_payment');
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_branch_name', 50)->nullable();
            // check payment details
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_cheque_number', 60)->nullable();
            $table->string('bank_micr_code', 60)->nullable();
            $table->date('bank_check_issue_date')->nullable();
            $table->date('bank_check_cleared_at')->nullable();
            $table->string('bank_cheque_image')->nullable();
            //online transaction details
            $table->string('sender_account_number', 100)->nullable();
            $table->string('receiver_account_number', 100)->nullable();
            $table->string('online_transaction_id', 100)->nullable();
            $table->enum('online_transfer_method', ['iBanking', 'BEFTN', 'RTGS', 'Mobile App', 'ATM Transfer', 'NPSB', 'Other'])
                ->nullable();

            // Card Details
            $table->string('card_number', 50)->nullable();
            $table->enum('card_type', ['Visa', 'MasterCard', 'American Express', 'Discover', 'UnionPay', 'JCB', 'Diners Club'])
                ->nullable();
            $table->string('last_four_digits', 4)->nullable();
            $table->string('card_expiry_date', 50)->nullable();
            $table->string('card_cvv', 50)->nullable();
            $table->string('card_image')->nullable();
            // Mobile Banking Details
            $table->string('mobile_banking_sender_number', 50)->nullable();
            $table->string('mobile_banking_receiver_number', 50)->nullable();
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
            ])->nullable();
            $table->string('mobile_banking_transaction_id', 100)->nullable();
            $table->enum('mobile_banking_account_type', ['Personal', 'Agent', 'Merchant'])->nullable();
            $table->string('mobile_banking_image')->nullable();
            $table->enum('status', ['processing', 'success', 'failed', 'refunded'])->default('success');
            $table->text('remarks')->nullable();
            $table->string('received_by')->nullable();
            $table->date('date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_purchese_payment_details');
    }
};
