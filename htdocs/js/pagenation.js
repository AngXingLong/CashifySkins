
function pagination(current,last){

	var x = document.getElementById("pagenation");
	var pagenation = "";
	
	if(current > 1){
		pagenation += "<span onclick='set_page("+(current-1)+")' class='page_highlight'>«</span>";
	}
	
	var i  = (1 > current-3) ? i = 1 : current-3; 
	
	
	while(true){
		if(i+6 > last && i != 1){
			i--;
		}else{
			break;
		}
	}
	
	
	var e = i+7;
	
	for (i; i < e; i++) { 
		
		if(i == current){
			pagenation += "<span onclick='set_page("+i+")' class='page_highlight'>"+i+"</span>";
		}
		else if(i > last){
			break;
		}
		else{
			pagenation += "<span onclick='set_page("+i+")' class='page'>"+i+"</span>";
		}
	}
	
	if(last > current){
		pagenation += "<span onclick='set_page("+(current+1)+")' class='page_highlight'>»</span>";
	}
	
	x.innerHTML = pagenation;
}
