var status = 0;
(function() {
    var mu = document.getElementsByClassName('music')[0];
    var audio = document.getElementById('mu');
    var music = function() {
        if (status == 0) {
            audio.pause();
            status = 1;
            mu.style.animationPlayState = "paused";
        } else if (status == 1) {
            audio.play();
            mu.style.animationPlayState = "running";
            status = 0;
        }
    };
    mu.onclick = function() {
        music();
    };
})();
(function() {
	var shopp=0;
    var obj = document.getElementById('obj');
    var button=document.getElementById('button');
    var botton1=document.getElementsByClassName('content5')[0];
    var objCNum = document.getElementsByClassName('content')[0].children;
    // var display;
    var objNum = 0;
    var objTouchY;
    var objTouchMY;
    button.onclick=function(){
    	console.log(botton1)
    	botton1.style.display='block';
    };
    if(shopp==0){
     obj.addEventListener('touchstart', function(event) {
        // console.log(event);
        objTouchY = event.changedTouches[0].pageY;
        // console.log(objTouchY);
    });
    obj.addEventListener('touchend', function(event) {
        // console.log(event);
        objTouchMY = event.changedTouches[0].pageY;
        // console.log(objTouchMY);
        Tab();
    });	
}
   

    function Tab() {
        // display=getComputedStyle(objCNum[0])['display'];
        if (objTouchY - objTouchMY > 60 && objNum != 3) {
            objCNum[objNum].style.display = "none";
            objNum += 1;
            objCNum[objNum].style.display = 'block';
            if (objNum == 0) {
                // anima([document.getElementsByClassName("C1Img")[0], "C1Img1"], [], 1);
                // anima([document.getElementsByClassName("C1Span")[0], "C1Span1"], [], 1);
            } else if (objNum == 1) {
                setTimeout(function() {
                    anima([document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], [document.getElementsByClassName("C1Img")[0], "C1Img1"], 0);
                    anima([document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], [document.getElementsByClassName("C1Span")[0], "C1Span1"], 0);
                }, 10);


                //anima([document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], [document.getElementsByClassName("C1Img")[0], "C1Img1"], 0);
                //anima([document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], [document.getElementsByClassName("C1Span")[0], "C1Span1"], 0);
            } else if (objNum == 2) {

            	 setTimeout(function() {
                    anima([document.getElementsByClassName("content3")[0].getElementsByTagName('h2')[0], "h3"],[document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], 0);
                    anima([document.getElementsByClassName("content3")[0].getElementsByClassName('C1Span')[0], "C1Span2"],[document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], 0);
                }, 10);

            } else if (objNum == 3) {
            		setTimeout(function() {
                    anima([document.getElementsByClassName("content4")[0].getElementsByClassName('shopPrice')[0], "shopPrice1"],[document.getElementsByClassName("content3")[0].getElementsByTagName('h2')[0], "h3"], 0);
                    anima([document.getElementsByClassName("content4")[0].getElementsByClassName('gouMai')[0], "gouMai1"],[document.getElementsByClassName("content3")[0].getElementsByClassName('C1Span')[0], "C1Span2"], 0);
                }, 10);


            }

        } else if (objTouchMY - objTouchY > 60 && objNum != 0) {
            objCNum[objNum].style.display = "none";
            objNum -= 1;
            objCNum[objNum].style.display = 'block';

            if (objNum == 0) {
                 setTimeout(function() {
                    anima([document.getElementsByClassName("C1Img")[0], "C1Img1"],[document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], 0);
                    anima( [document.getElementsByClassName("C1Span")[0], "C1Span1"],[document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], 0);
                }, 10);
            } else if (objNum == 1) {
                 setTimeout(function() {
                    anima([document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], [document.getElementsByClassName("content3")[0].getElementsByTagName('h2')[0], "h3"], 0);
                    anima([document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], [document.getElementsByClassName("content3")[0].getElementsByClassName('C1Span')[0], "C1Span2"], 0);
                }, 10);

                //anima([document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0], "h2"], [document.getElementsByClassName("C1Img")[0], "C1Img1"], 0);
                //anima([document.getElementsByClassName("content2")[0].getElementsByClassName('C1Span')[0], "C2Span1"], [document.getElementsByClassName("C1Span")[0], "C1Span1"], 0);
            } else if (objNum == 2) {

            	setTimeout(function() {
                    anima([document.getElementsByClassName("content3")[0].getElementsByTagName('h2')[0], "h3"],[document.getElementsByClassName("content4")[0].getElementsByClassName('shopPrice')[0], "shopPrice1"], 0);
                    anima([document.getElementsByClassName("content3")[0].getElementsByClassName('C1Span')[0], "C1Span2"],[document.getElementsByClassName("content4")[0].getElementsByClassName('gouMai')[0], "gouMai1"], 0);
                }, 10);

            } else if (objNum == 3) {

            }
        }
    };

    function anima(x, y, z) {
        // x===节点
        // y==类名
        // z==状态

        // console.log(document.getElementsByClassName("content2")[0].getElementsByTagName('h2')[0]);
        if (z == 0) {
            x[0].classList.remove(x[1]);
            y[0].classList.add(y[1]);
        } else if (z == 1) {
            // console.log(x[0]);
            x[0].classList.remove(x[1]);
        }
        else if(z==2){
        	y[0].classList.add(y[1]);
        }
    };
    setTimeout(function() {
        anima([document.getElementsByClassName("C1Img")[0], "C1Img1"], [], 1);
        anima([document.getElementsByClassName("C1Span")[0], "C1Span1"], [], 1);
    }, 1200);

    // anima(1,2);	

    // console.log(getComputedStyle(objCNum[0])['display']);
})();