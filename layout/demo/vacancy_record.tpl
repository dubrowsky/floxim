<div 
    fx:template="vacancy_record"
    fx:of="vacancy.record"
    fx:omit="true">
    <div fx:template="item" 
        class="clearfix vacancy-record">
        <h1 class="no-top-margin">{$position}</h1>
        <div>
    	   <div>
    	       <h3>{%Responsibilities}Responsibilities{/%}</h3>
    	       <div>{$responsibilities}</div>
    	   </div>
    	   <div>
    	       <h3>{%Requirements}Requirements{/%}</h3>
    	       <div>{$requirements}</div>
    	   </div>
    	   <div>
    	       <h3>{%conditions}Work Conditions{/%}</h3>
    	       <div>{$work_conditions}</div>
    	   </div>
    	   <h4 fx:if="$salary_from || $salary_to">
    	       {if $salary_from}{%from}From{/%}{$salary_from}{$currency} {/if}
    	       {if $salary_to}{%to}To{/%}{$salary_to}{$currency}{/if}
    	   </h4>
    	   <div>
    	       <h3>{%Contacts}Contacts{/%}</h3>
    	       <div fx:if="$phone">{%phone_tpl}Phone:{/%} {$phone}</div>
    	       <div fx:if="$email">{%email_tpl}Email: {/%} {$email}</div>
    	       <div fx:if="$contacts_name">{%name_tpl}Contact's name{/%}: {$contacts_name}</div>
    	   </div>
        </div>
    </div>
</div>