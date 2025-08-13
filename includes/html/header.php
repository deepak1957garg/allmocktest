<style type="text/css">
	body{
		font-family: "Roboto",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
		font-size : 19px;
		padding:0px;
		margin:0px;

	}
	a.but{
		display: inline-block;
		padding:6px;
		color:#000;
		font-size:14px;
		line-height: 20px;
		text-decoration: none!important;
		border: none;
		outline: 0;
	}
	
	a.button{
        color: #fff;
        line-height: 20px;
        font-weight: 300;
        background-color: #c10404;
        text-decoration: none!important;
        display: inline-block;
        box-sizing: border-box;
        cursor: pointer;
        border: none;
        height: 42px;
        border-radius: 4px;
        font-size: 16px;
        padding: 10px 20px;
        margin: 0;
        opacity: 1;
        outline: 0;
    }
    a.button-blue{
        color: #fff;
        line-height: 20px;
        font-weight: 300;
        background-color: #11A9FB;
        text-decoration: none!important;
        display: inline-block;
        box-sizing: border-box;
        cursor: pointer;
        border: none;
        height: 42px;
        border-radius: 4px;
        font-size: 16px;
        padding: 10px 30px;
        margin: 0;
        opacity: 1;
        outline: 0;
 		float:right;
 		width:150px;
 		text-align: center;
    }

    .head{
    	width: 100%;
    	position: fixed;
    	background: #fff;
    	z-index: 100000;
    }
	.topbar{
		position: relative;
	    z-index: 3;
	    background: #fff;
	    -webkit-tap-highlight-color: transparent;
	    margin:0 auto;
	    min-width: 280px;
	    width: 90%
	}
	.topbar::before {
	    display: block;
	    position: absolute;
	    top: -500px;
	    width: 100%;
	    height: 500px;
	    background: #fff;
	    content: "";
	}
	.topbar .container{
		display: flex;
	    flex-direction: row;
	    justify-content: space-between;
	    align-items: stretch;
	    position: relative;
	    padding: 16px 0;
	    margin: 0 auto;
	    min-width: 280px;
    	overflow-wrap: break-word;
	}
	.topbar .headline{
		display: flex;
	    flex-direction: row;
	    justify-content: flex-start;
	    align-items: center;
	    height: 48px;
	    max-width: 580px;
	    text-decoration: none;
	}
	.topbar img{
		height: 34px;
	}
	span.download{
			display: inline-block;
		}
	@media screen and (min-width: 912px){
		.topbar .container{ width: 900px; }
		a.but{ font-size: 16px; }
	}

	@media screen and (max-width: 362px){
		span.download{
			display: none;
		}
	}
	
</style>
<div class="head">
<div class="topbar">
	<div class="container">
		<a href="https://www.thesnug.app" native="true" class="headline">
			<img src="/images/substack/snug-logo-red-50h.png" alt="Snug"/>
		</a>
		<div>
			<?php echo $link; ?>
			<!-- <button class="button menu-button " type="button">
				<svg width="18" height="11" viewBox="0 0 18 11" fill="none" stroke-width="1" stroke="#000" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L9 9L17 1" stroke="#A4A4A4" stroke-width="2"></path></svg></button> -->
		</div>
	</div>
</div>
</div>