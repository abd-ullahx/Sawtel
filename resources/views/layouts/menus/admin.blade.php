@php $pending_support_ticket = request_count('support_ticket', true); @endphp

<li>
	<a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-tie"></i><span>{{ _lang('Clients') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('clients.index') }}">{{ _lang('All Clients') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('clients.create') }}">{{ _lang('Add New') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-box"></i><span>{{ _lang('Product & Services') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ _lang('Product & Services') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('product_units.index') }}">{{ _lang('Product Units') }}</a></li>
	</ul>
</li>

<li>
	<a href="{{ route('vendors.index') }}"><i class="fas fa-user-friends"></i><span>{{ _lang('Vendors') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-shopping-basket"></i><span>{{ _lang('Sales') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('invoices.index') }}">{{ _lang('Invoices') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('quotations.index') }}">{{ _lang('Quotations') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-shopping-bag"></i><span>{{ _lang('Purchases') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('purchases.create') }}">{{ _lang('New Purchase') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('purchases.index') }}">{{ _lang('All Purchases') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('purchases.index', ['type' => 'purchase_alert']) }}">{{ _lang('Purchases Alert') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-university"></i><span>{{ _lang('Accounting') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('accounts.index') }}">{{ _lang('Bank & Cash Accounts') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">{{ _lang('Transactions') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transaction_categories.index') }}">{{ _lang('Transaction Categories') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transaction_methods.index') }}">{{ _lang('Transaction Methods') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="far fa-chart-bar"></i><span>{{ _lang('Reports') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.sale-products-report') }}">{{ _lang('Sale Products Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.account_balances') }}">{{ _lang('Account Balances') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.account_statement') }}">{{ _lang('Account Statement') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.profit_and_loss') }}">{{ _lang('Profit & Loss Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.transactions_report') }}">{{ _lang('Transaction Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.income_by_customer') }}">{{ _lang('Income by Customer') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.purchase_by_vendor') }}">{{ _lang('Purchases by Vendor') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.tax_report') }}">{{ _lang('Tax Report') }}</a></li>
    </ul>
</li>


<li>
	<a href="javascript: void(0);"><i class="fas fa-building"></i><span>{{ _lang('Business Management') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('business.index') }}">{{ _lang('Manage Business') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('taxes.index') }}">{{ _lang('Tax Settings') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-tools"></i><span>{{ _lang('Administration') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('settings.update_settings') }}">{{ _lang('System Settings') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('database_backups.list') }}">{{ _lang('Database Backup') }}</a></li>
    </ul>
</li>

<li>
	<a href="{{ route('logout') }}">
		<i class="ti-power-off"></i>
		<span>{{ _lang('Logout') }}</span>
	</a>
</li>