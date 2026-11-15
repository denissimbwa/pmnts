        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="nk-sidebar">           
            <div class="nk-nav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label"><a href=" {{ route('account_client')}}"><i class="icon-speedometer menu-icon"></i>User Home</a></li>
                    <li class="nav-label"><a href="{{ route('user_centers')}}">My Centers</a></li>
                    <li class="nav-label"><a href="{{ route('user_accounts')}}">My Accounts</a></li>
                    <li class="nav-label"><a href="{{ route('invitation_list_admin',[\Session::get('user')->id])}}">My User Access</a></li>
                    <li class="nav-label"><a href="{{ route('clients_list_admin' )}}">API Clients</a></li>
                    <li class="nav-label"><a href="{{ route('deposit_mm_api' )}}">Deposit Mobile Money</a></li>
                    
<!--
{{--
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Group Admin</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('collection_shedule_list')}}">Group Collections</a></li>
                            <li><a href="{{ route('user_centers')}}">Pending Collections</a></li>
                            <li><a href="{{ route('user_centers')}}">Credits processed</a></li>
                            <li><a href="{{ route('user_centers')}}">Create new group</a></li>
                        </ul>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">My Accounts</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('user_accounts')}}">View My Accounts</a></li>
                            <li><a href="{{ route('user_accounts')}}">Groups Accounts</a></li>
                            <li><a href="{{ route('investment_summary_client')}}">Invesment</a></li>
                            <li><a href="{{ route('transaction_log_client')}}">Transactions History</a></li>
                            <li><a href="{{ route('transaction_log_client')}}">Failed Transactions</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">My Activities</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('user_centers')}}">Credit types</a></li>
                            <li><a href="{{ route('user_centers')}}">Investments</a></li>
                            <li><a href="{{ route('user_centers')}}">Discounts</a></li>
                            <li><a href="{{ route('user_centers')}}">Advance Pay</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">My Account Settings</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('user_centers')}}">Approvals</a></li>
                        </ul>
                    </li>
--}}
-->

                    @if(\Session::get('user')!=null && \Session::get('user')->user_rank !=="client")

                    <li class="nav-label"><a href=" {{ route('account_admin')}}"><i class="icon-speedometer menu-icon"></i>Admin Home</a></li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Centers</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('user_centers_admin')}}">View All Centers</a></li>
                            <li><a href="{{ route('user_centers_admin')}}">Unapproved Centers</a></li>
                            <li><a href="{{ route('user_centers_admin')}}">Deactivated Centers</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Payment Advance</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('advance_pay_centers_admin')}}">Advance Groups</a></li>
                            <li><a href="{{ route('advance_pay_centers_admin')}}">Unapproved Centers</a></li>
                            <li><a href="{{ route('advance_pay_centers_admin')}}">Inactive Centers</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Providers</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('provider_list_admin')}}">View All Providers</a></li>
                            <li><a href="{{ route('provider_list_admin')}}">Credit-givers</a></li>
                            <li><a href="{{ route('provider_list_admin')}}">Payment-givers</a></li>
                            <li><a href="{{ route('provider_list_admin')}}">Cash-loan Givers</a></li>
                            <li><a href="{{ route('provider_list_admin')}}">Discount-givers</a></li>
                        </ul>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Users</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('users_list_admin')}}">System USers</a></li>
                            <li><a href="{{ route('users_agents_admin')}}">Agent</a></li>
                            <li><a href="{{ route('users_list_admin')}}">Clients</a></li>
                            <li><a href="{{ route('users_list_admin')}}">Innactive Users</a></li>
                            <li><a href="{{ route('invitation_list_admin')}}">User Access</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Business Ventures</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('provider_services')}}">Business Lines</a></li>
                            <li><a href="{{ route('report_requests') }}">Report Requests</a></li>
                            <li><a href="{{ route('investment_summary_admin')}}">Investments Requests</a></li>
                        </ul>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Accounts</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('all_user_accounts',['admin'])}}">System Accounts</a></li>
                            <li><a href="{{ route('all_user_accounts',['user'])}}">User Accounts</a></li>
                            <li><a href="{{ route('all_user_accounts',['group'])}}">Group Accounts</a></li>
                            <li><a href="{{ route('all_user_accounts',['provider'])}}">Provider Accounts</a></li>
                            <li><a href="{{ route('push_transaction')}}"> Push Transaction</a></li>
                            <li><a href="{{ route('api_log_admin')}}"> Tx Log</a></li>
                        </ul>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Bank Activity</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('transaction_log_admin')}}">Transactions Log</a></li>
                            <li><a href="{{ route('transaction_log_admin')}}">Approved Transactions</a></li>
                            <li><a href="{{ route('user_centers_admin')}}">Rejected Approvals</a></li>
                            <li><a href="{{ route('user_centers_admin')}}">Withdraw Requests</a></li>
                            <li><a href="{{ route('user_centers_admin')}}">Withdraw Schedule</a></li>
                        </ul>
                    </li>

                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">System Settings</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('user_centers')}}">Bank Transafer</a></li>
                            <li><a href="{{ route('user_centers')}}">Approvals</a></li>
                            <li><a href="{{ route('user_centers')}}">Withdraw Limit</a></li>
                            <li><a href="{{ route('user_centers')}}">Center Limits</a></li>
                            <li><a href="{{ route('user_centers')}}">User Limits</a></li>
                            <li><a href="{{ route('user_centers')}}">API Selections</a></li>
                        </ul>
                    </li>
                    
                    
                    <hr/>
                    {{--
                    <li class="nav-label"><a href=" {{ route('account_provider',[$dta[0]->id])}}"><i class="icon-speedometer menu-icon"></i>Provider Home</a></li>
                    <li>
                        <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <span class="nav-text">Manage</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('request_credit_client')}}">Give Credit</a></li>
                            <li><a href="{{ route('transaction_log_provider')}}">Provider Activity</a></li>
                            <li><a href="{{ route('transaction_log_agent')}}">Agent Activity</a></li>
                        </ul>
                    </li>
                    --}}
@endif

                    <br>
                    <hr/>
                    <br>


                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
