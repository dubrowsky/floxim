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