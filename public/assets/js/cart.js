const ingredientAmountInputs = document.querySelectorAll('input[id^=amount]');

function stepDown(inputId) {
	const input = document.getElementById(inputId);
	input.stepDown();
	debounceTriggerChange(input);
}

function stepUp(inputId) {
	const input = document.getElementById(inputId);
	input.stepUp();
	debounceTriggerChange(input);
}

let debounceTimer;
function debounceTriggerChange(input, delay = 300) {
	clearTimeout(debounceTimer);
	debounceTimer = setTimeout(() => {
		triggerChange(input);
	}, delay);
}

function triggerChange(input) {
	const event = new Event('change', { bubbles: true });
	input.dispatchEvent(event);
}

ingredientAmountInputs.forEach(async (amountInput) => {
	amountInput.addEventListener('change', async () => {
		const formData = new FormData();
		formData.append('ingredientId', amountInput.id.split('amount').pop());
		formData.append('amount', amountInput.value);
		formData.append('csrfToken', csrfToken);

		await fetch(`${root}/ingredients/cart`, {
			method: 'POST',
			credentials: 'include',
			body: formData,
		}).then((res) =>
			console.log(
				res.statusText === 'OK' ? 'Updated Cart' : 'Could not update cart'
			)
		);
	});
});
