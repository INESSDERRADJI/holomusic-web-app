document.addEventListener('DOMContentLoaded', () => {
    const steps = document.querySelectorAll('.etape');
    let currentStep = 0;

    const nextBtn = document.getElementById('next');
    const backBtn = document.getElementById('back');
    const submitBtn = document.getElementById('submit-btn');

    steps.forEach((step, index) => {
        if (index === 0) {
            step.classList.add('etape-active');
            step.classList.remove('d-none');
        } else {
            step.classList.remove('etape-active');
            step.classList.add('d-none');
        }
    });

    backBtn.style.display = 'none';
    submitBtn.style.display = 'none';

    nextBtn.addEventListener('click', () => {
        const stepDiv = steps[currentStep];
        let errors = false;

        // Validation des inputs
        stepDiv.querySelectorAll('input').forEach(input => {
            const errorDiv = input.parentElement.querySelector('.error-message');
            errorDiv.style.display = 'none';
            input.classList.remove('error');

            const value = input.value.trim();

            if (!value) {
                errorDiv.innerText = 'This field is required';
                errorDiv.style.display = 'block';
                input.classList.add('error');
                errors = true;
            }

            if (input.name === 'mdp' && value.length > 0 && (value.length < 6 || value.length > 20)) {
                errorDiv.innerText = 'Password must be between 6 and 20 characters';
                errorDiv.style.display = 'block';
                input.classList.add('error');
                errors = true;
            }

            if (input.name === 'username' && value && !/^[a-zA-Z0-9_-]+$/.test(value)) {
                errorDiv.innerText = 'Username can only contain letters, numbers, _ and -';
                errorDiv.style.display = 'block';
                input.classList.add('error');
                errors = true;
            }

            if (input.name === 'email') {
                if (!value) {
                    errorDiv.innerText = 'This field is required';
                    errorDiv.style.display = 'block';
                    input.classList.add('error');
                    errors = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    errorDiv.innerText = 'Please enter a valid email';
                    errorDiv.style.display = 'block';
                    input.classList.add('error');
                    errors = true;
                }
            }


            if ((input.name === 'firstname' || input.name === 'name') && value && !/^[a-zA-ZÀ-ÿ '-]+$/.test(value)) {
                errorDiv.innerText = 'Only letters, spaces, - and \' allowed';
                errorDiv.style.display = 'block';
                input.classList.add('error');
                errors = true;
            }
        });
        
        if (errors) return;

        steps[currentStep].classList.remove('etape-active');
        steps[currentStep].classList.add('d-none');

        currentStep++;
        steps[currentStep].classList.add('etape-active');
        steps[currentStep].classList.remove('d-none');

        backBtn.style.display = currentStep > 0 ? 'inline-block' : 'none';

        if (currentStep === steps.length - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-block';
        } else {
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
        }
    });

    backBtn.addEventListener('click', () => {
        steps[currentStep].classList.remove('etape-active');
        steps[currentStep].classList.add('d-none');

        currentStep--;
        steps[currentStep].classList.add('etape-active');
        steps[currentStep].classList.remove('d-none');

        backBtn.style.display = currentStep > 0 ? 'inline-block' : 'none';
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    });
});
