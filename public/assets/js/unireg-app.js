
/* Extracted from unireg.view.php */

        // Terms validation with improved feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const termsCheckbox = document.getElementById('terms');
            const universitySelect = document.getElementById('university');
            const emailInput = document.getElementById('email');
            const universityDomains = {
                'university-of-colombo': 'cmb.ac.lk',
                'university-of-peradeniya': 'pdn.ac.lk',
                'university-of-sri-jayewardenepura': 'sjp.ac.lk',
                'university-of-kelaniya': 'kln.ac.lk',
                'university-of-moratuwa': 'uom.lk',
                'university-of-jaffna': 'jfn.ac.lk',
                'university-of-ruhuna': 'ruh.ac.lk',
                'eastern-university': 'esn.ac.lk',
                'south-eastern-university': 'seu.ac.lk',
                'rajarata-university': 'rjt.ac.lk',
                'sabaragamuwa-university': 'sab.ac.lk',
                'wayamba-university': 'wyb.ac.lk',
                'uva-wellassa-university': 'uwu.ac.lk',
                'open-university': 'ou.ac.lk',
                'buddhist-and-pali-university': 'bpuls.ac.lk',
                'sliit': 'sliit.lk',
                'nsbm': 'nsbm.ac.lk',
                'cinec': 'cinec.edu',
                'apiit': 'apiit.lk',
                'metropolitan-campus': 'kiu.ac.lk'
            };

            function isUniversityEmailMatch() {
                const university = universitySelect.value;
                const email = emailInput.value.trim().toLowerCase();

                if (!university || !email || !email.includes('@')) {
                    emailInput.setCustomValidity('');
                    return true;
                }

                const expectedDomain = universityDomains[university];
                if (!expectedDomain) {
                    emailInput.setCustomValidity('');
                    return true;
                }

                const emailDomain = email.split('@').pop();
                const isMatch = emailDomain === expectedDomain || emailDomain.endsWith('.' + expectedDomain);

                if (!isMatch) {
                    emailInput.setCustomValidity('University and Email Address mismatch.');
                    return false;
                }

                emailInput.setCustomValidity('');
                return true;
            }

            emailInput.addEventListener('input', isUniversityEmailMatch);
            universitySelect.addEventListener('change', isUniversityEmailMatch);

            // Add event listener to form submission
            form.addEventListener('submit', function(e) {
                if (!isUniversityEmailMatch()) {
                    e.preventDefault();
                    e.stopPropagation();
                    emailInput.reportValidity();
                    emailInput.focus();
                    return false;
                }

                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Add visual feedback
                    termsCheckbox.style.border = '2px solid #dc3545';

                    // Show alert
                    alert('Please agree to the Terms & Conditions and Privacy Policy to continue.');

                    // Focus on checkbox
                    termsCheckbox.focus();

                    // Remove visual feedback after 3 seconds
                    setTimeout(() => {
                        termsCheckbox.style.border = '2px solid #ccc';
                    }, 3000);

                    return false;
                }
            });

            // Remove error styling when checkbox is checked
            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    this.style.border = '2px solid #ccc';
                }
            });
        });
    
