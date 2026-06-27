<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table('email_templates')->insert([
			[
				"name" => "New Invoice Created",
				"slug" => "NEW_INVOICE_CREATED",
				"subject" => "New Invoice has been created",
				"email_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>New Invoice Created</h2> <p>Dear {{customerName}},</p> <p>I am writing to let you know that a new invoice has been created for the Product/Service you ordered. The details of the invoice are as follows:</p> <ul> <li>Invoice number: <strong>{{invoiceNumber}}</strong></li> <li>Invoice date: <strong>{{invoiceDate}}</strong></li> <li>Due date: <strong>{{dueDate}}</strong></li> <li>Total amount due: <strong>{{dueAmount}}</strong></li> </ul> <p>To make the payment, please use the following details:</p> <p>{{invoiceLink}}</p> <p>Thank you for your business. We appreciate your prompt payment.</p> </div>",
				"sms_body" => "New Invoice Created",
				"notification_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h4 style='color: #333333;'>New Invoice Created</h4> <p>Dear {{customerName}},</p> <p>I am writing to let you know that a new invoice has been created for the Product/Service you ordered. The details of the invoice are as follows:</p> <ul> <li>Invoice number: <strong>{{invoiceNumber}}</strong></li> <li>Invoice date: <strong>{{invoiceDate}}</strong></li> <li>Due date: <strong>{{dueDate}}</strong></li> <li>Total amount due: <strong>{{dueAmount}}</strong></li> </ul> <p>To make the payment, please use the following details:</p> <p class='my-2'><a class='btn btn-primary btn-xs' href='{{invoiceLink}}'>View Invoice</a></p> <p>Thank you for your business. We appreciate your prompt payment.</p> </div>",
				"shortcode" => "{{customerName}} {{invoiceNumber}} {{invoiceDate}} {{dueDate}} {{dueAmount}} {{invoiceLink}}",
				"email_status" => 0,
				"sms_status" => 0,
				"notification_status" => 0,
				"template_mode" => 0,
			],
			[
				"name" => "New Quotation Created",
				"slug" => "NEW_QUOTATION_CREATED",
				"subject" => "New Quotation Created",
				"email_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>New Quotation Created</h2> <p>Dear {{customerName}},</p> <p>I am pleased to inform you that we have created a new quotation for the Product/Service you requested. The details of the quotation are as follows:</p> <ul> <li>Quotation number: <strong>{{quotationNumber}}</strong></li> <li>Quotation date: <strong>{{quotationDate}}</strong></li> <li>Quotation expiry date: <strong>{{expiryDate}}</strong></li> <li>Total amount: <strong>{{amount}}</strong></li> </ul> <p>Please note that this quotation is valid until <strong>{{expiryDate}}</strong>. If you have any questions regarding the quotation, please do not hesitate to contact us.</p> <p><a href='{{quotationLink}}'>View Quotation</a></p> <p>To proceed with the order, please confirm your acceptance of the quotation by replying to this email. Once we receive your confirmation, we will proceed with the order and send you the invoice.</p> <p>Thank you for your interest in our products/services. We look forward to doing business with you.</p> </div>",
				"sms_body" => "Hi {{customerName}}, New Quotation {{quotationDate}} has been created",
				"notification_body" => "<h4 style='color: #333333;'>New Quotation Created</h4> <p>Dear {{customerName}},</p> <p>I am pleased to inform you that we have created a new quotation for the Product/Service you requested. The details of the quotation are as follows:</p> <ul> <li>Quotation number: <strong>{{quotationNumber}}</strong></li> <li>Quotation date: <strong>{{quotationDate}}</strong></li> <li>Quotation expiry date: <strong>{{expiryDate}}</strong></li> <li>Total amount: <strong>{{amount}}</strong></li> </ul> <p>Please note that this quotation is valid until <strong>{{expiryDate}}</strong>. If you have any questions regarding the quotation, please do not hesitate to contact us.</p> <p class='my-2'><a class='btn btn-primary btn-xs' href='{{quotationLink}}'>View Quotation</a></p> <p>To proceed with the order, please confirm your acceptance of the quotation by replying to this email. Once we receive your confirmation, we will proceed with the order and send you the invoice.</p> <p>Thank you for your interest in our products/services. We look forward to doing business with you.</p>",
				"shortcode" => "{{customerName}} {{quotationDate}} {{expiryDate}} {{amount}} {{quotationNumber}} {{quotationLink}}",
				"email_status" => 0,
				"sms_status" => 0,
				"notification_status" => 0,
				"template_mode" => 0,
			],
			[
				"name" => "Invoice Payment Reminder",
				"slug" => "INVOICE_PAYMENT_REMINDER",
				"subject" => "Invoice Payment Reminder",
				"email_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Invoice Payment Reminder</h2> <p>Dear {{customerName}},</p> <p>I hope this email finds you well. This message is to remind you that the payment for invoice <strong>{{invoiceNumber}}</strong> is now due.</p> <p>The total amount due is <strong>{{dueAmount}}</strong>. Please ensure that the payment is made promptly to avoid any late fees or penalties.</p> <p>To make the payment, kindly use the following details:</p> <p>{{invoiceLink}}</p> <p>If you have already made the payment, please disregard this email.</p> <p>Thank you for your prompt attention to this matter. If you have any questions or concerns, please do not hesitate to contact me.</p></div>",
				"sms_body" => "",
				"notification_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h4 style='color: #333333;'>Invoice Payment Reminder</h4> <p>Dear {{customerName}},</p> <p>I hope this email finds you well. This message is to remind you that the payment for invoice <strong>{{invoiceNumber}}</strong> is now due.</p> <p>The total amount due is <strong>{{dueAmount}}</strong>. Please ensure that the payment is made promptly to avoid any late fees or penalties.</p> <p>To make the payment, kindly use the following details:</p> <p class='my-2'><a class='btn btn-primary btn-xs' href='{{invoiceLink}}'>View Invoice</a></p> <p>If you have already made the payment, please disregard this email.</p> <p>Thank you for your prompt attention to this matter. If you have any questions or concerns, please do not hesitate to contact me.</p> </div>",
				"shortcode" => "{{customerName}} {{invoiceNumber}} {{invoiceDate}} {{dueDate}} {{dueAmount}} {{invoiceLink}}",
				"email_status" => 0,
				"sms_status" => 0,
				"notification_status" => 0,
				"template_mode" => 0,
			],
			[
				"name" => "Invoice Payment Received",
				"slug" => "INVOICE_PAYMENT_RECEIVED",
				"subject" => "Invoice Payment Received",
				"email_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h2 style='color: #333333;'>Invoice Payment Received</h2> <p>Dear {{customerName}},</p> <p>I am writing to confirm that we have received your payment for invoice <strong>{{invoiceNumber}}</strong>.</p> <p>The total amount received is <strong>{{amount}}</strong>.</p> <p>Thank you for your prompt payment. We value your business and look forward to working with you again in the future.</p> <p>If you have any questions or concerns, please do not hesitate to contact us.</p></div>",
				"sms_body" => "",
				"notification_body" => "<div style='font-family: Arial, sans-serif; font-size: 14px;'> <h4 style='color: #333333;'>Invoice Payment Received</h4> <p>Dear {{customerName}},</p> <p>I am writing to confirm that we have received your payment for invoice <strong>{{invoiceNumber}}</strong>.</p> <p>The total amount received is <strong>{{amount}}</strong>.</p> <p>Thank you for your prompt payment. We value your business and look forward to working with you again in the future.</p> <p>If you have any questions or concerns, please do not hesitate to contact us.</p> </div>",
				"shortcode" => "{{customerName}} {{amount}} {{invoiceDate}} {{paymentMethod}} {{invoiceNumber}} {{invoiceLink}}",
				"email_status" => 0,
				"sms_status" => 0,
				"notification_status" => 0,
				"template_mode" => 0,
			],
		]);
	}
}
