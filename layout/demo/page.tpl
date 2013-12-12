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
					<ul
					    fx:template="top_menu"
					    fx:name="Main menu"
					    fx:of="section.list"
					    class="main-menu">
						<li 
							fx:each="$items" 
							class="menu-item {if $children} dropdown{/if}">
							<a href="{$url}">{$name}</a>
							<ul fx:if="$children" class="menu-sub-items">
								<li fx:each="$children" fx:prefix="child" class="{if $child_active} active{/if} menu-sub-item">
									<a href="{$child_url}">{$child_name}</a>
								</li>
							</ul>
						</li>
					</ul>
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
					<div 
						fx:omit="true"
						fx:template="top_links"
						fx:of="section.list">
						<a fx:each="$items" href="{$url}">{$name}</a>
					</div>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div class="left-column" fx:area="left_column" fx:size="narrow,high" >
				<ul 
				    fx:template="left_menu"
					fx:name="Left menu"
				    fx:of="page.list"
				    class="sub-menu">
					<li fx:template="inactive" class="sub-menu-item">
						<a href="{$url}">{$name}</a>
					</li>
					<li fx:template="active" class="sub-menu-item active">
						<a href="{$url}">{$name}</a>
					</li>
				</ul>
			</div>	
			<div class="main-column" fx:area="main_column" fx:size="wide,high">
				<div
				    fx:template="index_slider" 
				    fx:name="Slider" 
				    fx:of="page.list"
				    fx:size="high,wide" 
				    class="slider fx_not_sortable">
					<div
						fx:each="$items" 
						class="slide {if $item_is_first}slide_active{/if} slideid{$id}">
						<a href="{$url}"><img src="{%bg_photo_$id}<?=$template_dir?>images/img01.jpg{/%}" alt="{$name}"></a>
						<div class="caption">
							<h2>
								<p>{%header_$id}{/%}</p>
							</h2>
							<div class="text">
								{%text_$id}<p></p>{/%}
							</div>
						</div>
					</div>
				    <div class="switcher">
				        <ul>
				            <li fx:each="$items" class="{if $item_is_first}active{/if} slideid{$id}" data-slideid="{$id}">
				                <a href="#" title="{$name}">{$item_index}</a>
				            </li>
				        </ul>
				    </div>
				    <a href="#" class="btn-prev">previous</a>
				    <a href="#" class="btn-next">next</a>
				</div>
				<div 
				    fx:template="featured_products_list"
				    fx:of="product.list"
				    class="featured-list">
					<div fx:each="$items" class="featured-item {if $item_index%3==0}last{/if}">
						<a href="{$url}">
							<img src="{$image}">
						</a>
						<div class="caption">
							{$price}
						</div>
					</div>
				</div>
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
				<div class="slider">
					<div class="slide">
						<img src="img/slide2.png" alt="">
						<div class="caption">
							<h2>
								<p>You are dead?</p>
								<p>Find new</p>	
							</h2>
							<div class="text">
								<p>Fuck</p>	
								<p>Find new</p>
							</div>
						</div>
					</div>
				</div>
				<div
					fx:template="featured_news_list"
				    fx:of="news.list"
				    class="featured-list four-items">
					<div 
						fx:template="item" 
						class="featured-item {if $item_index%4 == 0}last{/if}">
						<img fx:if="$image" src="{$image}">
						<div class="caption">
							{$anounce}
							<a href="{$url}">{$name}</a>
						</div>
					</div>

					<a class="more" href="{%more}">{%More_news}More news{/%}</a>
				</div>
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
				<div
					fx:template="news_list"
				    fx:of="news.list" 
				    class="news-list">
					<div 
						fx:template="item" 
						class="news-list-item">
						<div class="date">{$publish_date|'d.m.Y'}</div>
						<div class="announce">
							{$anounce}
							<a href="{$url}">{$name}</a>
						</div>
						<a class="badge">Space Times</a>
						<div style="clear:both;"></div>
					</div>
					<a class="more" href="{%more}">{%More_news}More news{/%}</a>
				</div>
			</div>
			<div class="main-column">
				<h2>{%sub_header}Pod Classes{/%}</h2>
				<div fx:area="main_column" fx:size="wide,high"></div>
			</div>
			<div class="right-column" fx:area="right_column" fx:size="narrow,high">
				<div 
					fx:template="simple_img"
					fx:of="photo.list" 
					class="img">
					<img fx:each="$items" src="{$photo}" alt="{$copy}"/>
				</div>
			</div>
			<div style="clear:both;"></div>
		</section>
		<footer>
			<div class="footer">
				<ul class="social-block">
					<li class="social-item">
						<a href="#"></a>
					</li>
					<li class="social-item">
						<a class="twitter" href="#"></a>
					</li>
					<li class="social-item">
						<a class="google" href="#"></a>
					</li>
					<li class="social-item">
						<a class="myspace" href="#"></a>
					</li>
					<li class="social-item">
						<a class="linkedin" href="#"></a>
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