const blocks = document.querySelectorAll(".grid-item");
blocks.forEach((block) => {
	block.addEventListener("mousedown", () => {
		console.log(block.id);
		let clicked = block.id;
		openPreviewModal(clicked.substring('item_'.length));
	});
});

const openPreviewModal = (blockid) => {
	removeModals();

	let backgroundTemplate = document.querySelector("#modal-background-template");
	let backgroundClone = backgroundTemplate.content.cloneNode(true);
	document.body.appendChild(backgroundClone);

	let newBlock = document.createElement("div");
	newBlock.setAttribute("id", blockid);
	let template = document.querySelector("#modal-preview-template");
	let clone = template.content.cloneNode(true);
	clone.querySelector(".modal-preview .image").src = "images/image (" + blockid + ").png";
	clone.querySelector(".modal-preview .choose-me").id = blockid;
	clone.querySelector(".modal-preview .choose-me").addEventListener("mousedown", () => {
		openPayModal(blockid);
	});
	clone.querySelector(".modal-preview .choose-another").addEventListener("mousedown", () => {
		removeModals();
	});
	document.body.appendChild(clone);
};

const openPayModal = async (blockid) => {
	let response = await fetch("stripe-create-paymentintent.php");
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	}
	let responseJson = await response.json();
	paymentIntentKey = responseJson.paymentintent_id;
	paymentClientSecret = responseJson.client_secret;

	const options = {
		clientSecret: paymentClientSecret,
		appearance: {
			theme: "stripe",
		},
	};

	let newBlock = document.createElement("div");
	newBlock.setAttribute("id", blockid);
	let template = document.querySelector("#modal-pay-template");
	let clone = template.content.cloneNode(true);
	clone.querySelector(".modal-pay .image").src = "images/image (" + blockid + ").png";
	document.body.appendChild(clone);

	const paymentElementDiv = document.querySelector("#payment-element");
	const elements = stripe.elements(options);
	const paymentElement = elements.create('payment');
	paymentElement.mount(paymentElementDiv);

	document
		.querySelector("#payment-form")
		.addEventListener("submit", (event) => {
			event.preventDefault();
			initPayment(paymentClientSecret, paymentIntentKey, elements, blockid);
		});
};



const initPayment = async (paymentClientSecret, paymentIntentKey, elements, blockid) => {

	requestURI = "stripe-update-paymentintent.php";
	const customer = new Object();
	const customerFields = document.querySelectorAll(".customer-fields input");
	customerFields.forEach((field) => {
		customer[field.name] = field.value;
	});

	customer.paymentintentkey = paymentIntentKey;
	customer.nft = blockid;

	let response = await fetch(requestURI, {
		method: "POST",
		body: JSON.stringify(customer),
	});
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	}

	const {
		error
	} = await stripe.confirmPayment({
		elements,
		redirect: 'if_required'
	});

	if (error) {
		console.log(error.message); // display this
	} else {


		const buttons = document.querySelectorAll("button");
		buttons.forEach((button) => {
			button.disable;
		});

		// start up poster and NFT creation systems
		const lookForFile = await stripeWebhookFile(blockid);
		statusMessage('payment', 'newline', '');
		statusMessage('payment', '', 'Payment Completed');

		createNFT(blockid);
		createPoster(blockid);
	}
};

const stripeWebhookFile = async (blockid) => {
	const interval = 2000;
	const maxAttempts = 30;
	let attempts = 0;
	statusMessage('payment', '', 'Processing Payment');
	const executePoll = async (resolve, reject) => {
		console.log('Oops 404.. just ignore that');
		const requestURI = 'minted/nft_minted_id_' + blockid
		let response = await fetch(requestURI)
		statusMessage('payment', '', '.');
		attempts++;
		// need to handle 404 better
		if (response.ok) {
			return resolve(response);
		} else if (maxAttempts && attempts === maxAttempts) {
			return reject(new Error('Exceeded max attempts'));
		} else {
			setTimeout(executePoll, interval, resolve, reject);
		}
	};
	return new Promise(executePoll);
}

const createNFT = async (blockid) => {
	const requestURI = 'crossmint-nft-maker.php?';
	// TODO include client secret or something for security
	let response = await fetch(requestURI + new URLSearchParams({
		nftid: blockid
	}));
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	}
	let result = await response.json();
	console.log('mint initiated for ' + result.recipient);
	statusMessage('mint', '', 'Mint Started');
	statusMessage('mint', 'newline', '');
	let completed = await checkNFT(result.crossmint_mint_id);
	console.log('completed', completed);
	statusMessage('mint', '', 'Mint Completed');
}

const checkNFT = async (crossmint_mint_id) => {
	const interval = 2000;
	const maxAttempts = 30;
	let attempts = 0;
	statusMessage('mint', '', 'Awaiting status');
	const executePoll = async (resolve, reject) => {
		console.log('Waiting for NFT to complete');
		statusMessage('mint', '', '.');
		const requestURI = 'crossmint-nft-checker.php?';
		let response = await fetch(requestURI + new URLSearchParams({
			nftid: crossmint_mint_id
		}));
		let result = await response.json();
		attempts++;
		console.log(result.status);
		if (result.status == 'success') {
			statusMessage('mint', 'newline', '');
			statusMessage('mint', '', 'Mint Completed');
			return result;
		} else if (maxAttempts && attempts === maxAttempts) {
			return reject(new Error('Exceeded max attempts'));
		} else {
			setTimeout(executePoll, interval, resolve, reject);
		}
	};
	return new Promise(executePoll);
}

const createPoster = async (blockid) => {
	statusMessage('print', '', 'Print request started');
	const requestURI = 'printful-order-poster.php?';
	// TODO include client secret or something for security
	let response = await fetch(requestURI + new URLSearchParams({
		nftid: blockid
	}));
	if (!response.ok) {
		throw new Error(`HTTP error! status: ${response.status}`);
	}
	result = await response.text();
	console.log('poster order status: ' + result);
	if (result == 'draft') {
		statusMessage('print', 'newline', '');
		statusMessage('print', '', 'Poster Order Completed');
	}


}

const statusMessage = (location, type, message) => {
	switch (location) {
		case 'payment':
			element = document.querySelector('.status-payment > div')
			break;
		case 'mint':
			element = document.querySelector('.status-mint > div')
			break;
		case 'print':
			element = document.querySelector('.status-print > div')
			break;
	}
	if (type == 'newline') {
		element.innerText += '\n';
	} else {
		element.innerText += message
	}

}


const removeModals = () => {
	const modals = document.querySelectorAll(".modal");
	modals.forEach((modal) => {
		modal.remove();
		document.querySelector(".modal-background").remove(); // bit ugly here
	});
}

const stripe = Stripe(
	"pk_test_abc"
);
