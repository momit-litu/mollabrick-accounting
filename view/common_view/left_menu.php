<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
	<div class="menu_section">
		<ul class="nav side-menu">
		<br><br><br><br>
			<li><a href="index.php"><i class="fa fa-home"></i>Dashboard</a></li>
			
			<li><a><i class="fa fa-money"></i>Accounts<span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu" style="display: none">
					<li><a class="menu-link"  href="index.php?module=income&view=income">Incomes</a></li>
                    <li><a class="menu-link"  href="index.php?module=expenses&view=expenses">Expenses</a></li>
                    <li><a class="menu-link"  href="index.php?module=liability&view=liability">Liability</a></li>
                    <li><a class="menu-link"  href="index.php?module=expenses&view=withdraw">Withdraw</a></li>
                    <li><a class="menu-link"  href="index.php?module=income&view=deposite">Deposit</a></li>
                    <hr>
                    <li><a class="menu-link"  href="index.php?module=income&view=incomeHead">Income Head</a></li>
                    <li><a class="menu-link"  href="index.php?module=expenses&view=expensesHead">Expense Head</a></li>
                    <li><a class="menu-link"  href="index.php?module=liability&view=liabilityHead">Liability Head</a></li>
				</ul>
			</li>    
			<?php if ($_SESSION['user_groups'] == 14) {?>
            <li><a><i class="fa fa-file"></i>Reports<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none">
                    <li><a class="menu-link"  href="index.php?module=reports&view=income">Incomes </a></li>
                    <li><a class="menu-link"  href="index.php?module=reports&view=expenses">Expenses </a></li>
                    <li><a class="menu-link"  href="index.php?module=reports&view=liability">Liability </a></li>
                    <hr>
                    <li><a class="menu-link"  href="index.php?module=reports&view=depositeWithdraw">Deposit & Withdraw</a></li>
                    <li><a class="menu-link"  href="index.php?module=reports&view=expensesIncomeReport">Expenses VS Income </a></li>
                    <li><a class="menu-link"   href="index.php?module=reports&view=summary">Summary </a></li>
                </ul>
            </li>
            <li><a><i class="fa fa-users"></i>Owner<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu" style="display: none">
                    <li><a class="menu-link"  href="index.php?module=hrm&view=user">Users</a></li>
                    <li><a class="menu-link"  href="index.php?module=hrm&view=stakeholders">Stakeholders</a></li>
					 <li><a class="menu-link"  href="index.php?module=project&view=project">Projects</a></li>
                </ul>
            </li>
			<?php 
			} 
			?>
           <!-- <li><a><i class="fa  fa-cogs"></i>Settings <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu" style="display: none">
					<li><a class="menu-link"  href="index.php?module=cp&view=group_permission">User Group</a></li>
					<li><a class="menu-link"  href="index.php?module=cp&view=actions">Web Actions</a></li>
				</ul>
			</li> -->
		</ul>
	</div>
</div>
