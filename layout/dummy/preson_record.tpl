<div 
    fx:template="person_record"
    fx:of="person.record"
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