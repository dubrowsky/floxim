<div
    fx:template="left_faq"
    fx:name="Left FAQ"
    fx:of="faq.list"
    fx:omit="true">
    <div 
        fx:each="$items" >
        <h4>{$question}</h4>
        <div>
            {$answer}
        </div>
    </div>
</div>