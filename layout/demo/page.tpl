<!DOCTYPE html>
<html>
<head>
    <meta fx:layout="two_columns" fx:name="Two columns" content="two_columns" />
    <meta fx:layout="three_columns" fx:name="Three columns" content="three_columns" />
    <meta fx:layout="one_column" fx:name="Full width" content="one_columns" />
    {js}
        FX_JQUERY_PATH
        js/script.js
        js/slider.js
    {/js}
    
    {css}
    	http://fonts.googleapis.com/css?family=Nixie+One
    	css/style.less
    {/css}
</head>
<body>
	<div class="wrapper">
		<nav>
			<div class="nav">
				<a href="/" class="logo">
					<img src="{%logo}<?=$template_dir?>img/logo.png{/%}">
				</a>
				<div fx:area="top_nav" fx:size="wide,low" class="menu-area">
				</div>
				
				<a class="phone" href="callto:{%phone}8.800.213.43.34{/%}">
					{%phone}8.800.213.43.34{/%}
				</a>
				<ul class="icons"  fx:area="icons_area" fx:size="narrow,low">
					<li class="icon search">
						<a href="#"></a>
					</li>
					<li class="icon <? echo fx::env('user') ? 'on' : 'off' ;?> login" fx:template="authform_popup" fx:of="widget_authform.show" >
						<a></a>
						<div class="auth_form">
							<form  method="POST" action="/floxim/" >
							    <input type="hidden" name="essence" value="module_auth" />
							    <input type="hidden" name="action" value="auth" />
								<div class="input-group">
									<label>Email</label>
									<input  name="AUTH_USER" type="text">
								</div>
								<div class="input-group">
									<label>Password</label>
									<input name="AUTH_PW" type="password">
								</div>
								<div class="input-group remember">
									<label>REMEMBER ME</label>
									<input type="checkbox">
								</div>
								<div class="input-group">
									<input type="submit" value="LOGIN">
								</div>
							</form>
						</div>
					</li>
				</ul>
			</div>
		</nav>
		<section fx:if="$two_columns" class="two-column">
			<div class="header">
				<h1 fx:each="$current_page">{$name}</h1>
				
				<div class="calendar" fx:area="header_links" fx:size="narrow,low">
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="left-column" fx:area="left_column" fx:size="narrow,high" >
			</div>	
			<div class="main-column" fx:area="main_column" fx:size="wide,high">
			</div>
			<div style="clear: both;"></div>
		</section>
		<section fx:if="$one_columns" class="one-column">
			<div class="header">
				<h1 fx:each="$path->last()">{$name}</h1>
				<div class="calendar" fx:area="header_links" fx:size="narrow,low">
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="main-column" fx:area="main_column" fx:size="wide,high">
			</div>
			<div style="clear: both;"></div>
		</section>
		<section fx:if="$three_columns" class="three-column">
			<div class="header">
				<h1 fx:each="$path->last()">{$name}</h1>
				<div class="calendar" fx:area="header_links" fx:size="narrow,low">
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="left-column" fx:area="left_column" fx:size="narrow,high" >
			</div>
			<div class="main-column">
				<div fx:area="main_column" fx:size="wide,high"></div>
			</div>
			<div class="right-column" fx:area="right_column" fx:size="narrow,high">
			</div>
			<div style="clear:both;"></div>
		</section>
		<footer>
			<div class="footer">
				<div class="social-area" fx:area="soc_area" fx:size="narrow,low"></div>
				<ul
					fx:template="social_icons"
					fx:of="social_icon.list"
					class="social-block">
					<li fx:each="$items" class="social-item">
						<a class="{$soc_type}" {if $soc_type=="unknown"}style="background:url({$icon|height:20,width:20});"{/if} href="{$url}"></a>
					</li>
				</ul>
				<div class="copyright">{%copyright}© Starwarslab{/%}</div>
				<div class="footer-links-area" fx:area="footer_links" fx:size="wide,low"></div>
				<ul 
					fx:template="footer_site_menu"
				    fx:name="Site map"
				    fx:of="section.list"
				    class="links">
					<li fx:each="$items"  class="link"><a href="{$url}">{$name}</a></li>
				</ul>
			</div>
			<div class="footer-menu"  fx:area="footer" fx:size="wide,low">
				<ul  
					fx:template="footer_menu"
				    fx:name="footer menu"
				    fx:of="section.list"
				    class="footer-menu-items">
					<li
						fx:each="$items"  
						class="footer-menu-item">
						<a href="{$url}">{$name}</a>
						<?dev_log('tpl', $children);?>
						<ul fx:if="$children" class="footer-menu-sub-items" >
							<li 
								fx:each="$children" 
								fx:prefix="child" 
								class="footer-menu-sub-item">
								<a href="{$child_url}">{$child_name}</a>
							</li>
						</ul>
					</li>
				</ul>
				<div style="clear:both;"></div>
			</div>
		</footer>
	</div>
</body>
</html>