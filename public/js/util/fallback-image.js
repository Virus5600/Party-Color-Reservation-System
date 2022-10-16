// Missing image handling
const head = document.getElementsByTagName('head')[0];
const img = document.getElementsByTagName('img');

head.innerHTML = `
	${head.innerHTML}
	<style>
		@keyframes img-fallback-missing-pulse {
			0% {
				opacity: 0.125;
			}
			50% {
				opacity: 1;
			}
			100% {
				opacity: 0.125;
			}
		}

		.img-fallback-missing {
			position: relative;
			overflow: hidden;
			width: 100%;
			height: 100%;
		}

		.img-fallback-missing::before {
			content: '';
			position: absolute;
			width: 100%;
			height: 100%;
			box-shadow: 0px 3px 10px #ff0000;
			-webkit-animation: 1.25s cubic-bezier(0.5, 1, 0.5, 1) img-fallback-missing-pulse;
			-moz-animation: 1.25s cubic-bezier(0.5, 1, 0.5, 1) img-fallback-missing-pulse;
			-ms-animation: 1.25s cubic-bezier(0.5, 1, 0.5, 1) img-fallback-missing-pulse;
			-o-animation: 1.25s cubic-bezier(0.5, 1, 0.5, 1) img-fallback-missing-pulse;
			animation: 1.25s cubic-bezier(0.5, 1, 0.5, 1) img-fallback-missing-pulse;
			
			-webkit-animation-iteration-count: infinite;
			-moz-animation-iteration-count: infinite;
			-ms-animation-iteration-count: infinite;
			-o-animation-iteration-count: infinite;
			animation-iteration-count: infinite;
		}
	</style>
`;

let i = 1;
Array.from(img).forEach((v) => {
	v.addEventListener('error', function fallbackImageOnErrorReplace(e) {
		let obj = e.currentTarget;
		// console.log(e.target);

		// console.log(obj)
		if (obj.getAttribute('data-fallback-image') !== null && obj.getAttribute('data-fallback-image') != 'none') {
			obj.src = obj.getAttribute('data-fallback-image');
		}
		else if (obj.getAttribute('data-fallback-img') != 'none' && obj.getAttribute('data-fallback-img') !== null) {
			obj.src = obj.getAttribute('data-fallback-img');
		}
		else {
			if (typeof fiFallbackImage != 'undefined' || typeof fiFallbackImg != 'undefined') {
				let fiFallbackImgURL = typeof fiFallbackImg != 'undefined' ? fiFallbackImg : fiFallbackImage;
				obj.src = fiFallbackImgURL;
			}
			else {
				obj.id = 'imgFallbackMissing' + (i++);
				obj.classList.add('img-fallback-missing');
				console.warn('It seems that this element does not have a fallback image:\n', (window.location.href.indexOf('#') < 0 ? window.location.href : window.location.href.substr(0, window.location.href.indexOf('#'))) + "#" + obj.id);
			}
		}
		obj.removeEventListener('error', fallbackImageOnErrorReplace);
	});
});