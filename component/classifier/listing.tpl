<div 
    fx:template="entity_classifier" 
    fx:of="listing" 
    fx:name="Classifier" 
    class="classifier">
        {%tags_label}Classifieres:{/%} 
        <a fx:template="item" href="{$url}">
             {$name}
        </a>
        <span fx:template="separator">, </span>
</div>