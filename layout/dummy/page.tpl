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
            bootstrap.js
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
                    <div class="col-xs-3">
                        <div class="logo">
                            <a href="/">
                                <img src="{%logo}<?=$template_dir?>images/logo.png{/%}" alt="" />
                                <div>{%slogan}Lorem ipsum dolor sit{/%}</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-2 pull-right text-center">
                        <div class="row">
                            <h3 class="col-xs-6 lang"><a href="#de">DE</a></h3>
                            <h3 class="col-xs-6"><a class="col-xs-6" href="#en">EN</a></h3>
                        </div>
                    </div>
                    <div class="col-xs-2 pull-right text-right">
                        <div class="row">
                            <div class="col-xs-12"><h5>{%hi}Hi, {/%}<? echo fx::env('user') ? fx::env('user')->get('name') : 'guest' ;?></h5></div>    
                        </div>
                        <div class="row">
                            <a class="col-xs-12  log-in">
                                
                                <? if(!fx::env('user')) { ?>
                                {%Login}Log in{/%}
                                <div  fx:area="log-in" class="form panel panel-default">
                                </div>
                                <? } ?>
                            </a>
                        </div>
                    </div>
                    <a class="col-xs-2 pull-right text-right" href="#">
                        <div class="row">
                            <div class="col-xs-12"><h4>{%cart}Shopping Cart{/%}</h4></div>    
                        </div>
                        <div class="row">
                            <div class="col-xs-12">12 items</div>
                        </div>
                    </a>
                    <div class="col-xs-2 pull-right">
                        <h4>{%phone}8.800.213.43.34{/%}</h4>
                        <h4>{%add_phone}8.800.213.43.34{/%}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <nav
                            class="navbar navbar-default" role="navigation">
                            <div class="navbar-header">
                                <a href="/" class="navbar-brand log-in" >
                                    {%Home}Home{/%}
                                </a>      
                            </div>
                            <div fx:area="menu" fx:size="wide,low" >
                            </div>
                        </nav>            
                    </div>
                </div>
                <div fx:if="$index_areas" class="row three-blocks">
                    <div fx:area="left_block" fx:size="narrow,high" class="col-xs-4">
                        <div fx:template="block_titled" fx:of="block" class="thumbnail">
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>
                    </div>
                    <div fx:area="center_block" fx:size="narrow,high" class="col-xs-4">
                    </div>
                    <div fx:area="right_block" fx:size="narrow,high" class="col-xs-4">
                    </div>
                </div>
                <div fx:if="$index_areas" class="row">
                    <div fx:area="bottom_wide_block" fx:size="wide,low" class="col-xs-12">   
                        
                        <div fx:template="block_titled_bottom" fx:of="block" >
                            <h2>{%header}Header{/%}</h2>
                            {$content}
                        </div>     
                    </div>
                </div>
                <div fx:if="$index_areas" class="row index-areas">
                    <div fx:area="bottom_left_block" fx:size="narrow,low" class="col-xs-6">
                        
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
                    </div>
                    <div fx:area="bottom_right_block" fx:size="narrow,low" class="col-xs-6 ">
                    </div>
                </div>
                <div fx:if="$two_columns" class="row">
                    <div fx:template="block_page"
                             fx:of="block"
                             fx:omit="true" >
                             <h1 class="no-top-margin">{%header}Header{/%}</h1>
                             <div>
                                {$content}
                             </div>
                    </div>
                    <div fx:area="columns_left_block" fx:size="narrow,high" class="col-xs-3">
                    </div>
                    <div fx:area="two_columns_right_block" fx:size="wide,high" class="col-xs-9">
                    </div>
                </div>
                <div fx:if="$three_columns" class="row">
                    <div fx:area="columns_left_block" fx:size="narrow,high" class="col-xs-3">
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
                        
                        
                    </div>
                    <div fx:area="three_columns_middle_block" fx:size="wide,high" class="col-xs-6">
                    </div>
                    <div fx:area="three_columns_right_block" fx:size="narrow,high" class="col-xs-3 three-columns-right-block">
                    </div>
                </div>
                <div fx:if="$full" class="row">
                    <div fx:area="full_width_block" fx:size="wide,high" class="col-xs-12">
                        <iframe width="100%" height="600px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.ru/maps?hl=ru&amp;ie=UTF8&amp;ll=55.793827,37.604227&amp;spn=0.021375,0.066047&amp;t=m&amp;z=15&amp;output=embed"></iframe>
                        <br />
                        <small>
                            <a href="https://maps.google.ru/maps?hl=ru&amp;ie=UTF8&amp;ll=55.793827,37.604227&amp;spn=0.021375,0.066047&amp;t=m&amp;z=15&amp;source=embed" style="color:#0000FF;text-align:left">Просмотреть увеличенную карту</a>
                        </small>                
                    </div>
                </div>
                <div class="row row_footer">
                    <div fx:area="footer" fx:size="wide,low"  class="col-xs-12">
                        <div class="col-xs-4">
                            <h4>
                                <div class="col-xs-4">{%call_us}Call us:{/%}</div> 
                                <div class="col-xs-8">
                                    <div>{%phone}8.800.213.41.14{/%}</div>
                                    <div>{%add_phone}8.800.213.41.14{/%}</div>
                                </div>
                            </h4>
                        </div>
                        <div class="col-xs-6"></div>
                        <div class="col-xs-2">
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