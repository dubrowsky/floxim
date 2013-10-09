<!DOCTYPE html>
<html>
    <head>
        <meta fx:layout="two_columns" fx:name="Two columns" content="two_columns" />
        <meta fx:layout="three_columns" fx:name="Three columns" content="three_columns" />
        <meta fx:layout="index" fx:name="Index" content="index_areas" />
        <meta fx:layout="full" fx:name="Full width" content="full" />
        {js}
            FX_JQUERY_PATH
            script.js
        {/js}
        
        {css}
            bootstrap.css
            default.css
        {/css}
    </head>
    <body>
        <div class="bootstrap">
            <div class="container">
                <div class="row on-top">
                    <div class="col-md-3">
                        <div class="logo">
                            <a href="/">
                                <img src="{%logo}<?=$template_dir?>images/logo.png{/%}" alt="" />
                                <div>{%slogan}Lorem ipsum dolor sit{/%}</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2 pull-right text-center">
                        <div class="row">
                            <h3 class="col-md-6 lang"><a href="#de">DE</a></h3>
                            <h3 class="col-md-6"><a class="col-md-6" href="#en">EN</a></h3>
                        </div>
                    </div>
                    <div class="col-md-2 pull-right text-right">
                        <div class="row">
                            <div class="col-md-12"><h5>{%hi}Hi, {/%}<? echo fx::env('user') ? fx::env('user')->get('name') : 'guest' ;?></h5></div>    
                        </div>
                        <div class="row">
                            <a class="col-md-12  log-in">
                                
                                <? if(!fx::env('user')) { ?>
                                {%Login}Log in{/%}
                                <div  fx:area="log-in" class="form panel panel-default">
                                    <form class="panel-body" fx:template="authform_popup" fx:of="widget_authform.show"  method="POST" action="/floxim/" >
                                        <input type="hidden" name="essence" value="module_auth" />
                                        <input type="hidden" name="action" value="auth" />
                                        <div class="input-group">
                                            <span class="input-group-addon">@</span>
                                            <input type="text" name="AUTH_USER" class="form-control" placeholder="Username">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-addon">*</span>
                                            <input type="password" name="AUTH_PW" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary">Log In</button>
                                        </div>
                                    </form>
                                </div>
                                <? } ?>
                            </a>
                        </div>
                    </div>
                    <a class="col-md-2 pull-right text-right" href="#">
                        <div class="row">
                            <div class="col-md-12"><h4>{%cart}Shopping Cart{/%}</h4></div>    
                        </div>
                        <div class="row">
                            <div class="col-md-12">12 items</div>
                        </div>
                    </a>
                    <div class="col-md-2 pull-right">
                        <h4>{%phone}8.800.213.43.34{/%}</h4>
                        <h4>{%add_phone}8.800.213.43.34{/%}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav
                            class="navbar navbar-default" role="navigation">
                            <div class="navbar-header">
                                <a href="/" class="navbar-brand log-in" >
                                    {%Home}Home{/%}
                                </a>      
                            </div>
                            <div fx:area="menu" fx:size="wide,low" style="overflow:hidden;">
                                <ul
                                    fx:template="top_menu"
                                    fx:name="Main menu"
                                    fx:of="component_section.listing"
                                    class="nav navbar-nav">
                                    <li fx:template="inactive"><a href="{$url}">{$name}</a></li>
                                    <li fx:template="active" class="active"><a href="{$url}">{$name}</a></li>
                                </ul>
                                <div fx:template="searchline" fx:of="widget_search.show" class="col-md-3 pull-right search-line">
                                    <div class="input-group">
                                        <input type="text" class="form-control">
                                        <span class="input-group-btn">
                                            <input class="btn btn-default" type="button" value="{%go}Go!{/%}" />
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </nav>            
                    </div>
                </div>
                <div fx:if="$index_areas" class="row three-blocks">
                    <div fx:area="left_block" fx:size="narrow,high" class="col-md-4">
                        <div fx:template="block_titled" fx:of="block" class="thumbnail">
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>
                        <div 
                            fx:template="main_photo"
                            fx:of="component_photo.listing"
                            fx:omit="true" >
                            <div 
                                fx:each="$items"
                                fx:omit="true" >
                                <img src="{$photo|'width:320,crop:middle'}" alt="{$copy}">
                                <div class="caption">
                                    <p>{$description}</p>
                                    <h4>{$copy}</h4>
                                </div>
                            </div>
                         </div>
                        <div 
                            fx:template="main_news"
                            fx:of="component_news.listing"
                            fx:omit="true" >
                            <div 
                                fx:each="$items"
                                class="caption" >
                                <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                                <div>
                                    {$anounce}
                                </div>
                                <div class="text-right text-muted">{$publish_date|'d.m.Y'}</div>
                            </div>
                         </div>
                        <div 
                            fx:template="main_projects"
                            fx:of="component_project.listing"
                            fx:omit="true" >
                            <div 
                                fx:each="$items"
                                class="caption" >
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="{$image|'width:90'}" alt="{$name}">
                                    </div>
                                    <div class="col-md-8">
                                        <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                                        <div>
                                            {$short_description}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 text-left">
                                        Client: <span class="text-info">{$client}</span>
                                    </div>
                                    <div class="col-md-6 text-right text-muted">
                                        {$date|'m.Y'}
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    <div fx:area="center_block" fx:size="narrow,high" class="col-md-4">
                    </div>
                    <div fx:area="right_block" fx:size="narrow,high" class="col-md-4">
                    </div>
                </div>
                <div fx:if="$index_areas" class="row">
                    <div fx:area="bottom_wide_block" fx:size="wide,low" class="col-md-12">   
                        
                        <div fx:template="block_titled_bottom" fx:of="block" >
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>
                        <div
                            fx:template="bottom_thumbs"
                            fx:of="component_photo.listing"
                            fx:omit="true">
                            <div
                                fx:each="$items"
                                class="col-md-3 thumbnail">
                                <img src="{$photo}" alt="{$copy}">
                            </div>
                        </div>  
                        <div
                            fx:template="featured_products"
                            fx:of="component_product.listing"
                            class="featured-products">
                            <a
                                fx:each="$items"
                                href="{$url}"
                                class="col-md-3 thumbnail">
                                <img src="{$image|'height:190,crop:middle'}" alt="{$name}">
                                <div class="caption text-center">
                                    <h4>{$name}</h4>
                                    <h5>{$price}</h5>
                                </div>
                            </a>
                        </div>  
                        <div
                            fx:template="product_record"
                            fx:of="component_product.record"
                            fx:omit="true" >
                            <div
                                fx:template="item"
                                class="col-md-12">
                                <h1 class="no-top-margin">{$name}</h1>
                                <div class="col-md-6">
                                    <img src="{$image}" alt="{$name}">
                                </div>
                                <div class="col-md-6">
                                    <h4 class="no-top-margin">{$reference}</h4>
                                    <div>
                                        {$description}
                                    </div>
                                    <h3>{$price}</h3>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
                <div fx:if="$index_areas" class="row index-areas">
                    <div fx:area="bottom_left_block" fx:size="narrow,low" class="col-md-6">
                        
                        <div fx:template="block_titled_bottom_left"
                             fx:of="block" 
                             class="panel panel-default" >
                            <div class="panel-heading">
                                <h3 class="panel-title">{%header}Header{/%}</h3>
                            </div>
                            <div class="panel-body">
                            {$content}
                            </div>
                        </div>
                        <div
                            fx:template="main_person"
                            fx:of="component_person.listing"
                            fx:omit="true">
                            <div
                                fx:each="$items"
                                fx:omit="true" >
                                <div class="col-md-4">
                                    <img src="{$photo|'width:145,height:150,crop:middle'}" alt="{$full_name}">
                                </div>
                                <div class="col-md-8">
                                    <h3 class="no-top-margin"><a href="{$url}">{$full_name}</a></h3>
                                    <h4>{$position}</h4>
                                    <div>
                                        {$short_description}
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div fx:area="bottom_right_block" fx:size="narrow,low" class="col-md-6 ">
                        <div
                            fx:template="bottom_photo"
                            fx:of="component_photo.listing"
                            fx:omit="true">
                            <div
                                fx:each="$items"
                                fx:omit="true" >
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <img src="{$photo|'height:150,crop:middle'}" alt="{$copy}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-right text-muted">
                                        {$copy}
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div fx:if="$two_columns" class="row">
                    <div
                        fx:template="products_listing"
                        fx:of="component_product.listing"
                        fx:omit="true">
                        <a
                            fx:each="$items"
                            href="{$url}"
                            class="col-md-3 thumbnail">
                            <img src="{$image|'height:190,crop:middle'}" alt="{$name}">
                            <div class="caption text-center">
                                <h4>{$name}</h4>
                                <h5>{$price}</h5>
                            </div>
                        </a>
                    </div>   
                    <div fx:template="block_page"
                             fx:of="block"
                             fx:omit="true" >
                             <h1 class="no-top-margin">{%header}Header{/%}</h1>
                             <div>
                                {$content}
                             </div>
                    </div>
                    <div fx:area="columns_left_block" fx:size="narrow,high" class="col-md-3">
                        <ul 
                            fx:template="left_menu"
    						fx:name="Left menu"
                            fx:of="component_section.listing"
                            class="list-group">
                                <li fx:template="inactive" class="list-group-item"><a href="{$url}">{$name}</a></li>
                                <li fx:template="active" class=" list-group-item active"><a class="active" href="{$url}">{$name}</a></li>
                        </ul>
                        <ul 
                            fx:template="categories_menu"
    						fx:name="Categories Menu"
                            fx:of="component_product_category.listing"
                            class="list-group">
                                <li fx:template="inactive" class="list-group-item"><a href="{$url}">{$name}</a></li>
                                <li fx:template="active" class=" list-group-item active"><a class="active" href="{$url}">{$name}</a></li>
                        </ul>
                    </div>
                    <div fx:area="two_columns_right_block" fx:size="wide,high" class="col-md-9">
                        <div 
                            fx:template="news_listing"
                            fx:of="component_news.listing"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="news-item clearfix">
                                <div class="col-md-3">
                                    <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
                                </div>
                                <div class="col-md-8 col-md-offset-1">
                                    <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                                    <h5>{$publish_date|'d.m.Y'}</h5>
                                    <div>{$anounce}</div>
                                    {call id="component_classifier.entity_classifier"}{$items select="$tags" /}{/call}
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="news_record"
                            fx:of="component_news.record"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="clearfix news-record">
                                <h1 class="no-top-margin">{$name}</h1>
                                <h3>{$publish_date|'d.m.Y'}</h3>
                                <div>
                                    <img src="{$image}" alt="{$full_name}" class="pull-left">
                                    <div>{$text}</div>
                                    {call id="component_classifier.entity_classifier"}{$items select="$item['tags']" /}{/call}
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="preson_record"
                            fx:of="component_person.record"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="clearfix person-record">
                                <h1 class="no-top-margin">{$full_name}</h1>
                                <h2>{$position}</h2>
                                <h3>{$company}</h3>
                                <div>
                                    <img src="{$photo}" alt="{$full_name}" class="pull-left">
                                    <div>{$description}</div>
                                    {call id="component_contact.entity_contact"}{$items select="$item['contacts']" /}{/call}
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="preson_listing"
                            fx:of="component_person.listing"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="person-item clearfix">
                                <div class="col-md-3">
                                    <img src="{$photo|'width:200px,crop:middle'}" alt="{$full_name}">
                                </div>
                                <div class="col-md-8 col-md-offset-1">
                                    <h3 class="no-top-margin"><a href="{$url}">{$full_name}</a></h3>
                                    <h4>{$position}</h4>
                                    <h5>{$company}</h5>
                                    <div>{$short_description}</div>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="vacancy_listing"
                            fx:of="component_vacancy.listing"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="vacancy-item clearfix">
                                <div class="col-md-12">
                                    <h3 class="no-top-margin"><a href="{$url}">{$position}</a></h3>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="vacancy_record"
                            fx:of="component_vacancy.record"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="clearfix vacancy-record">
                                <h1 class="no-top-margin">{$position}</h1>
                                <div>
                            	   <div>
                            	       <h3>{%Responsibilities}Responsibilities{/%}</h3>
                            	       {$responsibilities}
                            	   </div>
                            	   <div>
                            	       <h3>{%Requirements}Requirements{/%}</h3>
                            	       {$requirements}
                            	   </div>
                            	   <div>
                            	       <h3>{%conditions}Work Conditions{/%}</h3>
                            	       {$work_conditions}
                            	   </div>
                            	   <div fx:if="$salary_from || $salary_to">
                            	       {if $salary_from}From {$salary_from} {/if}
                            	       {if $salary_to}To {$salary_to}{/if}
                            	   </div>
                            	   <div>
                            	       <h3>{%Contacts}Contacts{/%}</h3>
                            	       <div fx:if="$phone">Phone: {$phone}</div>
                            	       <div fx:if="$email">Email: {$email}</div>
                            	       <div fx:if="$contacts_name">{%name}Contact's name{/%}: {$contacts_name}</div>
                            	   </div>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="award_record"
                            fx:of="component_award.record"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="clearfix award-record">
                                <h1 class="no-top-margin">{$name}</h1>
                                <h3>{$year}</h3>
                                <div>
                                    <img src="{$image}" alt="{$name}" class="pull-left">
                                    <div>{$description}</div>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="award_listing"
                            fx:of="component_award.listing"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="award-item clearfix">
                                <div class="col-md-3">
                                    <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
                                </div>
                                <div class="col-md-8 col-md-offset-1">
                                    <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                                    <h5>{$year}</h5>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="project_record"
                            fx:of="component_project.record"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="clearfix project-record">
                                <h1 class="no-top-margin">{$name}</h1>
                                <h2>{$client}</h2>
                                <h3>{$date|'m.Y'}</h3>
                                <div>
                                    <img src="{$image}" alt="{$name}" class="pull-left">
                                    <div>{$description}</div>
                                </div>
                            </div>
                        </div>
                        <div 
                            fx:template="project_listing"
                            fx:of="component_project.listing"
                            fx:omit="true">
                            <div fx:template="item" 
                                class="project-item clearfix">
                                <div class="col-md-3">
                                    <img src="{$image|'width:200px,crop:middle'}" alt="{$name}">
                                </div>
                                <div class="col-md-8 col-md-offset-1">
                                    <h3 class="no-top-margin"><a href="{$url}">{$name}</a></h3>
                                    <h4>{$client}</h4>
                                    <h5>{$date|'m.Y'}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div fx:if="$three_columns" class="row">
                    <div fx:area="columns_left_block" fx:size="narrow,high" class="col-md-3">
                        <div 
                            fx:template="block_primary_block"
                            fx:of="block"
                            class="panel panel-primary" >
                                <div class="panel-heading">
                                    <h3 class="panel-title">{%header}Header{/%}</h3>
                                </div>
                                <div class="panel-body">
                                   {$content}
                                </div>
                        </div>
                        <div 
                            fx:template="block_info_block"
                            fx:of="block"
                            class="panel panel-info" >
                                <div class="panel-heading">
                                    <h3 class="panel-title">{%header}Header{/%}</h3>
                                </div>
                                <div class="panel-body">
                                   {$content}
                                </div>
                        </div>
                        <div 
                            fx:template="block_success_block"
                            fx:of="block"
                            class="panel panel-success" >
                                <div class="panel-heading">
                                    <h3 class="panel-title">{%header}Header{/%}</h3>
                                </div>
                                <div class="panel-body">
                                   {$content}
                                </div>
                        </div>
                        
                        <div
                            fx:template="left_faq"
                            fx:name="Left FAQ"
                            fx:of="component_faq.listing"
                            fx:omit="true">
                            <div 
                                fx:each="$items" >
                                <h4>{$question}</h4>
                                <div>
                                    {$answer}
                                </div>
                            </div>
                        </div>
                        
                        <div
                            fx:template="right_vacancies"
                            fx:name="Right Vacancies"
                            fx:of="component_vacancy.listing"
                            fx:omit="true">
                            <div 
                                fx:each="$items"
                                class="vacancy-sm-item" >
                                <h4><a href="{$url}">{$name}</a></h4>
                            </div>
                        </div>
                        <div
                            fx:template="right_awards"
                            fx:name="Right Awards"
                            fx:of="component_award.listing"
                            fx:omit="true">
                            <div 
                                fx:each="$items"
                                class="award-sm-item" >
                                <h4><a href="{$url}">{$name}</a></h4>
                                <div>
                                    {$short_description}
                                </div>
                                <div class="text-right">
                                    {$year}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div fx:area="three_columns_middle_block" fx:size="wide,high" class="col-md-6">
                    </div>
                    <div fx:area="three_columns_right_block" fx:size="narrow,high" class="col-md-3 three-columns-right-block">
                    </div>
                </div>
                <div fx:if="$full" class="row">
                    <div fx:area="full_width_block" fx:size="wide,high" class="col-md-12">
                        <iframe width="100%" height="600px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ru/maps?hl=ru&amp;ie=UTF8&amp;ll=55.793827,37.604227&amp;spn=0.021375,0.066047&amp;t=m&amp;z=15&amp;output=embed"></iframe><br /><small><a href="https://maps.google.ru/maps?hl=ru&amp;ie=UTF8&amp;ll=55.793827,37.604227&amp;spn=0.021375,0.066047&amp;t=m&amp;z=15&amp;source=embed" style="color:#0000FF;text-align:left">Просмотреть увеличенную карту</a></small>                
                    </div>
                </div>
                <div class="row">
                    <div fx:area="footer" fx:size="wide,low"  class="col-md-12">
                        <div class="col-md-4">
                            <h4>
                                <div class="col-md-4">{%call_us}Call us:{/%}</div> 
                                <div class="col-md-8">
                                    <div>{%phone}8.800.213.41.14{/%}</div>
                                    <div>{%add_phone}8.800.213.41.14{/%}</div>
                                </div>
                            </h4>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-2">
                            <address>
                                <h4>© {%brand}Brand{/%}</h4>
                                <a href="mailto:{%email}info@jt.com{/%}">{%email}info@jt.com{/%}</a> 
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>