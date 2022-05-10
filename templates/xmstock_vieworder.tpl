<div class="xmstock">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
		<li class="breadcrumb-item"><a href="order.php"><{$smarty.const._MA_XMSTOCK_ORDERS}></a></li>
		<{if $error_message|default:'' == ''}>
			<li class="breadcrumb-item active" aria-current="page"><{$smarty.const._MA_XMSTOCK_VIEWORDER}></li>
		<{/if}>
	  </ol>
	</nav>
	<{if $error_message|default:'' != ''}>
		<div class="alert alert-danger" role="alert"><{$error_message}></div>
	<{else}>
		<div class="row mb-2">
			<div class="col-md-12">
				<div class="card border border-primary">
					<div class="card-header bg-primary text-white">
						<div class="d-flex justify-content-between">
							<h3 class="mb-0 text-white"><{$smarty.const._MA_XMSTOCK_VIEWORDER_ORDER}><{$orderid}></h3>
							<div class="row align-items-center text-right">
								<div class="col">
									<span class="badge badge-secondary fa-lg text-primary ml-2"><small> <{$status}></small></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row border-bottom border-secondary mx-1 pl-1">
						<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
							  <span class="fa fa-calendar fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMSTOCK_ORDER_ORDERDATE}>
							  <figcaption class="figure-caption text-center"><{$dorder}></figcaption>
						</figure>
						<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
							  <span class="fa fa-repeat fa-fw" aria-hidden="true"></span> <{$smarty.const._MA_XMSTOCK_ORDER_ORDERDESIRED}>
							  <figcaption class="figure-caption text-center"><{$ddesired}></figcaption>
						</figure>
						<figure class="figure text-muted my-1 pr-2 text-center border-right border-secondary">
							
							<{if $delivery == 1}>
								<span class="fa fa-truck fa-fw"></span><{$smarty.const._MA_XMSTOCK_ORDER_DELIVERY}>
								<figcaption class="figure-caption text-center"> <{$smarty.const._MA_XMSTOCK_ORDER_DELIVERY_DELIVERY}></figcaption>
							<{/if}>
							<{if $delivery == 0}>
							<span class="fa fa-industry fa-fw"></span><{$smarty.const._MA_XMSTOCK_ORDER_DELIVERY}>
								<figcaption class="figure-caption text-center"><{$smarty.const._MA_XMSTOCK_ORDER_DELIVERY_WITHDRAWAL}></figcaption>
							<{/if}>
						</figure>
					</div>
					<div class="card-body">
						<p class="card-text mb-auto">
							<div class="row">
								<div class="col">
									<p>
									<{$description}>
									</p>
								</div>
							</div>
						</p>
						<hr>
						<h4>Articles commandés </h4>
						<div class="col-12 pl-2 pr-2 pb-2">
							<div class="card">
								<div class="card-header">
									<{$smarty.const._MA_XMSTOCK_CADDY_ITMES}>
								</div>
								<hr>
								<div class="card-body">
									<div class="row">
										<{foreach item=item from=$item}>
											<div class="col-12 col-md-6 p-2">
												<div class="row">
													<div class="col-6">
														<b><{$item.name}></b><br>
														<{$item.cid}>
													</div>
													<div class="col-6">
														<{$item.amount}>
													</div>
												</div>
											</div>
										<{/foreach}>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<{/if}>
</div><!-- .xmstock -->