<?php
/**
 * Custom Css
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="css w-embed">
	<style>
		html{font-size:calc(100vw / 1440)}
		@media screen and (max-width:992px){
		html{font-size:calc(100vw / 756)}
		}
		@media screen and (max-width:495px){
		html{font-size:calc(100vw / 375)}
		}
		.oyziv-item {
		flex: 0 !important;
		}
		.catalog-page-grid .prod-item_img-mom{
		min-height: 436rem;
		}
		.otzivi-grid > *:nth-child(6n + 1),
		.otzivi-grid > *:nth-child(6n + 5){width:569rem;max-width:569rem;flex-shrink:0;
		min-width: 569rem;}
		.otzivi-grid > *:nth-child(6n + 2),
		.otzivi-grid > *:nth-child(6n + 6){width:342rem;max-width:342rem;flex-shrink:0;
		min-width: 342rem;}
		.otzivi-grid > *:nth-child(6n + 3),
		.otzivi-grid > *:nth-child(6n + 4){width:458rem;max-width:458rem;flex-shrink:0;
		min-width: 458rem;}
		/* .catalog-grid > *:nth-child(6n + 3),
		.catalog-grid > *:nth-child(6n + 4){
		grid-area: span 2 / span 2 / span 2 / span 2;
		} */
		.active .color-mom{border-color:black}
		.breadcrumb a{color: black;text-decoration:none}
		.breadcrumb ul{display: flex;
		padding: 0;
		margin: 0;
		list-style-type: none;
		}
		.breadcrumb li{display: flex;
		align-content: center;
		flex-wrap: wrap;
		align-items: center;
		}
		.breadcrumb li:after{
		content: "";
		margin-left: 10rem;
		margin-right: 10rem;
		border-radius: 100%;
		background: black;
		width: 2px;
		height: 2px;
		display: block;
		}
		.breadcrumb li:nth-last-child(1):after{display:none}
		.likes-svg svg{opacity:0.2}
		.likes-svg.l1 svg:nth-child(1){opacity:1}
		.likes-svg.l2 svg:nth-child(1),
		.likes-svg.l2 svg:nth-child(2){opacity:1}
		.likes-svg.l3 svg:nth-child(1),
		.likes-svg.l3 svg:nth-child(2),
		.likes-svg.l3 svg:nth-child(3){opacity:1}
		.likes-svg.l4 svg:nth-child(1),
		.likes-svg.l4 svg:nth-child(2),
		.likes-svg.l4 svg:nth-child(3),
		.likes-svg.l4 svg:nth-child(4){opacity:1}
		.likes-svg.l5 svg{opacity:1}
		.no-image .likes-svg svg path{fill:#801f80}
		.slider-oyziv_nav.w-slider-nav.w-num > div {
		font-size: inherit;
		line-height: inherit;
		width: auto;
		height: auto;
		padding: .2em .5em;
		padding: 0;
		border-radius: 0;
		margin: 0;
		display: none;
		margin: 0 !important;
		}
		.slider-oyziv_nav.w-slider-nav.w-num > div.w-slider-dot.w-active{display:block}
		.slider-oyziv_nav.w-slider-nav.w-num div.slider-oyziv-last{display:block}
		.oyziv-item.no-image {
		background-position-x: calc(100% - 30rem);
		background-position-y: 30rem;
		}
		.otziv-left-ul > li:before{content:"";
		width: 3px;
		height: 3px;
		transform: rotate(45deg);
		flex-shrink: 0;
		background: #F22EA9;
		margin-right:20rem;
		position:relative;
		top:8rem;
		}
		.otziv-left-ul > li{display:flex;margin-bottom:20rem;}
		.video-block video{position:static; height:auto}
		.flex-center{
		position: absolute;
		left: 0;
		top: 0;
		z-index: 2;
		width: 100%;
		height: 100%;
		display: flex;
		align-content: center;
		align-items: center;
		justify-items: center;
		flex-wrap: wrap;
		text-align: center;
		align-items: end;
		lex: none;
		justify-content: center;
		align-items: center;
		display: flex;
		}
		.dyn-content > *:nth-child(1),
		.dyn-content > .rich-single >*:nth-child(1){margin-top:0}
		.logo-keeper{pointer-events:none}
		.logo-keeper a{pointer-events:all}
		.hovered-menue.active{display:grid}
		.fixed-navbar .lik path,
		.fixed-navbar .pink-svg path,
		.white-top header .lik path,
		.white-top header .pink-svg path,
		.dopmenuopened .lik path,
		.dopmenuopened .pink-svg path
		{fill:#F22EA9}
		.dopmenuopened .choosed-item{color:white}
		.dopmenuopened .logo-link path{fill:#F22EA9}
		.fixed-navbar a.w--current,
		.white-top header a.w--current{color:black}
		.white-top .logo-link path{fill:#F22EA9}
		.white-top .indirm-line{color:white}
		.white-top  .menu-line,
		html:has(.dopmenuopened ) .menu-line{
		border-bottom: 1px solid #0003;
		}
		.white-top .b-line{background:black}
		.fixed-navbar .b-line{background:black}
		.white-top > section:nth-child(1) .page-top {
		padding-top: calc(236rem - 0rem);
		}
		.white-top > section:nth-child(1) .page-top.single-p {
		padding-top: calc(142rem - 0rem);
		}
		.hovered-menue .a-12-12,
		.hovered-menue .n-menu.w--current {color:black}
		.foo-top .soc-btn {
		border: 1px solid #ffffff1a;
		}
		.foo-top path {
		fill: white;
		}
		.marq {
		animation: marquee 20s linear infinite;
		will-change: transform
		}
		@keyframes marquee {
		0% { transform: translateX(0%) translateZ(0); }
		100% { transform: translateX(-50%) translateZ(0); }
		}
		.fixed-navbar .hovered-menue {
		padding-top: 80rem;
		}
		.choosed-item{color:white !important}
		.serach-btn .code-embed-3:nth-child(2){display:none}
		.serach-btn.serach-open .code-embed-3:nth-child(1){display:none}
		.serach-btn.serach-open .code-embed-3:nth-child(2){display:flex}
		.search-ajaxed{display:none}
		html:has(.search-input:not(:placeholder-shown)) .hovered-menue.search-m .m-h-vert,
		html:has(.search-input:not(:placeholder-shown)) .hovered-menue.search-m .div-block-6._3{
		display:none
		}
		html:has(.search-input:not(:placeholder-shown)) .search-ajaxed{display:flex}
		/* .search-ajaxed_item .search-ajaxed_item_0px{display:none}
		.search-ajaxed_item:hover .search-ajaxed_item_0px{display:flex} */
		.input-file input {
		font-size: 0;
		background-image: url('https://cdn.prod.website-files.com/653b83c22352f8081d9d44ea/661e66ae664669f80bb3691d_file.svg');
		width: 30rem;
		height: 30rem;
		background-position: center;
		background-size: contain;
		background-repeat: no-repeat;
		position: absolute;
		cursor: pointer;
		}
		/*.input-file input:hover{*/
		/*    -webkit-filter: brightness(10);*/
		/*    filter: brightness(10);*/
		/*}*/
		.file-item__name {
		color: #8d8d8d;
		font-size: 16rem;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 252rem;
		width: 252rem;
		display: block;
		}
		.input-file {
		position: absolute;
		bottom:calc(100% + 3px);
		width: 30rem;
		height: 30rem;
		right: 0;
		}
		.file-item {
		display: flex;
		position: relative;
		top: 30rem;
		}
		.file-item__remove {
		width: 16rem;
		height: 16rem;
		margin-left: 15rem;
		background: none;
		font-size: 0;
		display: flex;
		position: relative;
		color: #8d8d8d;
		}
		.file-item__remove:after,
		.file-item__remove:before {
		content: '';
		display: block;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto;
		width: 1.5px;
		height: 16rem;
		-webkit-transform-origin: center;
		transform-origin: center;
		background: #8d8d8d;
		}
		.file-item__remove:hover:after,
		.file-item__remove:hover:before {
		background: white;
		}
		.file-item__remove:after {
		-webkit-transform: rotate(45deg);
		transform: rotate(45deg);
		}
		.file-item__remove:before {
		-webkit-transform: rotate(-45deg);
		transform: rotate(-45deg);
		}
		.input-file-list:has(.file-item) + label{display:none}
		.input-file {
		position: static;
		bottom: calc(100% + 3px);
		width: auto;
		height: auto;
		right: 0;
		right: 0;
		cursor: pointer;
		}
		.input-file input {
		font-size: 0;
		background-image: none;
		width: auto;
		height: auto;
		background-position: center;
		background-size: contain;
		background-repeat: no-repeat;
		position: static;
		cursor: pointer;
		appearance: none;
		-webkit-appearance: none;
		opacity: 0;
		}
		.file-item {
		display: flex;
		position: relative;
		top: 0rem;
		}
		.file-item__name {
		color: black;
		font-size: 12rem;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: none;
		width: auto;
		display: block;
		line-height: 14rem;
		}
		.file-item__remove {
		width: 10rem;
		height: 10rem;
		margin-left: 0;
		background: none;
		font-size: 0;
		display: flex;
		position: relative;
		color: #8d8d8d;
		color: transparent;
		order: -1;
		margin-right: 10rem;
		}
		.file-item__remove:after, .file-item__remove:before {
		content: '';
		display: block;
		position: absolute;
		top: 0;
		bottom: 0;
		margin: auto;
		width: 1px;
		height: 10rem;
		-webkit-transform-origin: center;
		transform-origin: center;
		background: black;
		}
		.file-item__remove:hover:after, .file-item__remove:hover:before {
		background: black;
		}
		.input-file-list{
		position: absolute;
		bottom: 0;
		right: 20rem;
		display: flex;
		gap: 10rem;
		}
		.input-file-list-item{
		width: 40rem;
		position: relative;
		height: 60rem;
		}
		.input-file-list-img{
		object-fit:cover;
		width:100%;
		height:100%}
		.input-file-list-name{display:none}
		.input-file-list-remove{
		position: absolute;
		z-index: 2;
		top: -9px;
		right: -9px;
		}
		.input-file {
		margin: 0;
		}
		@media screen and (max-width:992px){
		.catalog-page-grid .prod-item_img-mom{
		min-height: 0px;
		}
		/* .catalog-grid > *:nth-child(6n + 3), .catalog-grid > *:nth-child(6n + 4) {
		grid-area: span 1 / span 1 / span 1 / span 1;
		} */
		.ui-slider-horizontal {
		height: 6px;
		width: auto;
		flex: 1;
		}
		.code-embed-7:after,.code-embed-7:before{display:none}
		}
		@media screen and (max-width:495px){
		.prod-item .p-12-12,
		.otziv-horiz .p-12-12,
		.blog-horiz .p-12-12{font-size:12rem;line-height:12rem}
		.catalog-page-grid .prod-item{width:auto;margin-left:-20rem;margin-right:-20rem;}
		}
		.mobmenuopened .b-line{background-color:black}
		.mobmenuopened .b-line:nth-child(1){transform: rotate(45deg) translateY(0);}
		.mobmenuopened .b-line:nth-child(2){transform: scale(0);}
		.mobmenuopened .b-line:nth-child(3){transform: rotate(-45deg) translateY(0);}
		.mobmenuopened .choosed-item {
		color: white
		}
		.mobmenuopened .logo-link path {
		fill: #F22EA9
		}
		.mobmenuopened a, .mobmenuopened .p-12-12.uper.white {
		color: black;
		}
		.mobmenuopened .lik path, .mobmenuopened .pink-svg path
		{
		fill: #F22EA9;
		}
		.mobmenuopened .serach-btn path{fill:black}
		.mobmenuopened  a .indirm-line_div{color:white}
		.mobmenuopened .mob-menue{display:flex}
		.htmldopmenuopened{overflow:hidden}
		.hovered-menue.mob-menue {
		position: absolute;
		bottom: auto;
		top: 0;
		height: 100vh;
		}
		@media screen and (max-width:992px){
		.hovered-menue.active {
		display: flex;
		flex-direction: column; 
		padding-top: 150rem;
		}
		html:has(.search-input:not(:placeholder-shown)) .search-ajaxed {
		display: flex;
		font-size: 12rem;
		line-height: 12rem;
		grid-column-gap: 25rem;
		grid-row-gap: 25rem;
		}
		.clear-search {
		font-size: 12rem;
		line-height: 12rem;
		}
		::-webkit-scrollbar {
		height:0;
		width:0
		}
		::-webkit-scrollbar-track {
		background:black
		}
		::-webkit-scrollbar-thumb {
		background-color:#c1c1c1;
		border-radius:0;
		border:0 solid #c1c1c1
		}
		}
		.select {
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		}
		.select::-ms-expand {
		display: none;
		}
	</style>
</div>
