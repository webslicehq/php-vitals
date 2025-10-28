(function($) {
    'use strict';

    let isRunning = false;
    let testCount = 0;
    let totalTime = 0;
    let iterations = 0;
    let testOutput = [];

    function formatTime(time) {
        let seconds = time % 60;
        return `${Math.floor(time / 60).toString().padStart(2, '0')}:${Math.floor(seconds).toString().padStart(2, '0')}.${Math.floor(seconds % 1 * 1000).toString().padStart(3, '0')}`;
    }

    function getGrade(time) {
        return new Promise((resolve, reject) => {
            if (!phpvitals || !phpvitals.ajaxurl) {
                reject(new Error('phpvitals object or ajaxurl not available'));
                return;
            }

            const formData = new FormData();
            formData.append('action', 'phpvitals_get_grades');
            formData.append('nonce', phpvitals.nonce);
            formData.append('time', time);

            fetch(phpvitals.ajaxurl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    try {
                        if (data.success && data.data) {
                            let grades = data.data;
                            if (!Array.isArray(grades)) {
                                console.error('Grades response is not an array:', grades);
                                reject('Invalid grades data format');
                                return;
                            }
                            for (let grade of grades) {
                                if (time <= grade.avg_value) {
                                    resolve(grade);
                                    return;
                                }
                            }
                            resolve(grades[grades.length - 1]);
                        } else {
                            reject(data.data || 'Error getting grades');
                        }
                    } catch (error) {
                        console.error('Error processing grades response:', error);
                        reject('Error processing grades: ' + error.message);
                    }
                })
                .catch(error => {
                    console.error('getGrade error:', error);
                    reject(error);
                });
        });
    }

    function createTest(data) {
        if (!data || typeof data.test_name === 'undefined') {
            console.warn('Invalid data passed to createTest:', data);
            return;
        }

        testCount++;

        let tbody = $('#testResultsTable tbody').length ? $('#testResultsTable tbody')[0] : null;
        if (!tbody) return;

        let row = tbody.insertRow();
        let nameCell = row.insertCell(0);
        nameCell.textContent = data.test_name;

        if (data.skipped) {
            let cell = row.insertCell(1);
            cell.textContent = data.skip_reason || 'Skipped';
            cell.colSpan = 3;
            cell.style.color = '#64748b';
            cell.style.fontStyle = 'italic';
        } else {
            if (typeof data.time === 'number' && typeof data.ops_per_ms === 'number') {
                row.insertCell(1).textContent = data.time.toFixed(5) + 's';
                row.insertCell(2).textContent = Math.round(data.ops_per_ms) + ' op/ms';
                totalTime += data.time;
                iterations += data.iterations;
                testOutput.push(data);
            } else {
                console.warn('Invalid time or ops_per_ms data:', data);
            }
        }
    }

    function addTotalsRow() {
        let tfoot = $('#testResultsTable tfoot');
        if (!tfoot || !tfoot.length) return;

        if (typeof totalTime !== 'number' || isNaN(totalTime)) {
            console.warn('Invalid totalTime:', totalTime);
            return;
        }

        const totalRow = $('<tr>');

        totalRow.css({
            'borderTop': '2px solid ',
        });
        totalRow.html(`
            <td>Totals</td>
            <td><strong>${totalTime.toFixed(5)}s</strong></td>
        `);
        tfoot.append(totalRow);
    }

    function processBenchmarkResults() {
        try {
            addTotalsRow();

            getGrade(totalTime).then(grade => {
                try {
                    if (!grade || typeof grade.grade === 'undefined') {
                        console.warn('Invalid grade data:', grade);
                        return;
                    }

                    let gradeContainer = $('.grade-container');
                    let gradeDisplay = $('#gradeDisplay');
                    let gradeDescription = $('#gradeDescription');

                    if (gradeContainer && gradeContainer.length && gradeDisplay && gradeDisplay.length && gradeDescription && gradeDescription.length) {
                        gradeContainer.show();
                        gradeDisplay.text(grade.grade);
                        if (grade.colour) {
                            gradeDisplay.addClass(grade.colour);
                        }
                        gradeDisplay.addClass('grade-circle');
                        if (grade.desc) {
                            gradeDescription.text(grade.desc);
                        }
                    }

                    setTimeout(() => {
                        try {
                            saveBenchmarkResults(grade);
                        } catch (saveError) {
                            console.error('Error saving results:', saveError);
                        }
                    }, 500);
                } catch (gradeError) {
                    console.error('Error processing grade:', gradeError);
                }
            }).catch(error => {
                console.error('Error getting grade:', error);
            });
        } catch (processError) {
            console.error('Error in processBenchmarkResults:', processError);
        }
    }

    function saveBenchmarkResults(grade) {
        try {

            const formData = new FormData();
            formData.append('action', 'phpvitals_save_results');
            formData.append('nonce', phpvitals.nonce);
            formData.append('total_time', totalTime);
            formData.append('iterations', iterations);
            formData.append('test_count', testCount);
            formData.append('grade', grade.grade);
            formData.append('output', JSON.stringify(testOutput));

            fetch(phpvitals.ajaxurl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        updateLoadingMessage('Failed to save benchmark results: ' + data.data.message);
                    }
                })
                .catch(error => {
                    updateLoadingMessage('Error saving benchmark results: ' + error.message);
                });
        } catch (error) {
            console.error('Error in saveBenchmarkResults:', error);
        }
    }

    function updateLoadingMessage(message) {
        const loading = $('#loading');
        if (loading && loading.length > 0) {
            try {
                loading.html(message);
            } catch (error) {
                console.error('Error updating loading message:', error);
            }
        }
    }

    function runTest(testIndex = 0) {
        const formData = new FormData();
        formData.append('action', 'phpvitals_run_benchmark');
        formData.append('nonce', phpvitals.nonce);
        formData.append('test_index', testIndex);

        fetch(phpvitals.ajaxurl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    const data = response.data;

                    if (!data || typeof data.total_tests === 'undefined') {
                        console.error('Invalid response data:', data);
                        updateLoadingMessage('Error: Invalid test data received');
                        isRunning = false;
                        $('#runBenchmark').prop('disabled', false);
                        return;
                    }

                    updateTestProgress(data);
                    createTest(data);

                    if (testIndex < data.total_tests - 1) {
                        setTimeout(() => {
                            runTest(testIndex + 1, null);
                        }, 1000);
                    } else {
                        isRunning = false;
                        $('#runBenchmark').prop('disabled', false);
                        jQuery('#loading').hide();
                        jQuery('#testResultsTable').show();

                        processBenchmarkResults();
                    }
                } else {
                    updateLoadingMessage('Error running benchmark: ' + response.data.message);
                    isRunning = false;
                    $('#runBenchmark').prop('disabled', false);
                }
            })
            .catch(error => {
                updateLoadingMessage('Error running benchmark: ' + error);
                isRunning = false;
                $('#runBenchmark').prop('disabled', false);
            });
    }

    function updateTestProgress(data) {
        const loading = $('#loading');
        if (!loading || !loading.length) return;

        if (!data || typeof data.test_index === 'undefined' || typeof data.total_tests === 'undefined' || typeof data.test_name === 'undefined') {
            console.warn('Invalid data passed to updateTestProgress:', data);
            return;
        }

        const message = `Running test ${data.test_index + 1}/${data.total_tests}: ${data.test_name}`;
        updateLoadingMessage(message);
    }

    jQuery(document).ready(function($) {

        const runButton = $('#runBenchmark');

        function startBenchmark() {
            if (isRunning) return;

            isRunning = true;
            runButton.prop('disabled', true);
            $('#loading').show();
            $('#gradeDisplay').text('--');
            $('#gradeDescription').text('--');

            $('#testResultsTable tbody').empty();
            $('#testResultsTable tfoot').empty();

            totalTime = 0;
            testCount = 0;
            iterations = 0;

            runTest(0, null);
        }

        runButton.on('click', startBenchmark);

        $('#unified-form').on('submit', function(e) {
            e.preventDefault();

            const hostingType = $('input[name="hosting_type"]:checked').val();
            const hostingCost = $('input[name="hosting_cost"]:checked').val();
            const termsAccepted = $('#terms-accept').prop('checked') ? 'on' : 'off';

            if (!hostingType || !hostingCost) {
                showMessage('Please select both hosting type and cost', 'error');
                return;
            }

            $.ajax({
                url: phpvitals.ajaxurl,
                type: 'POST',
                data: {
                    action: 'phpvitals_save_hosting_info',
                    nonce: phpvitals.nonce,
                    hosting_type: hostingType,
                    hosting_cost: hostingCost,
                    terms_accept: termsAccepted
                },
                success: function(response) {
                    if (response.success) {
                        $('#unified-form').hide();
                        $('#hosting-info-section').hide();
                        $('.current-status-display').show();

                        updateHostingInfoDisplay(response.data);

                        showMessage('Information saved successfully!', 'success');

                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showMessage('Error: ' + (response.data || 'Failed to save information'), 'error');
                    }
                },
                error: function() {
                    showMessage('Error: Failed to save information', 'error');
                }
            });
        });

        $('#edit-hosting-info').on('click', function() {
            $('.current-status-display').hide();
            $('#unified-form').show();
            $('#hosting-info-section').show();
        });

        $('#cancel-edit').on('click', function() {
            $('#unified-form').hide();
            $('#hosting-info-section').hide();
            $('.current-status-display').show();

            resetFormToCurrentValues();
        });


        function updateHostingInfoDisplay(data) {

            setTimeout(function() {
                location.reload();
            }, 1000);
        }

        function resetFormToCurrentValues() {
            $('input[type="radio"]').prop('checked', false);
        }

        function showMessage(message, type) {
            const messageClass = type === 'success' ? 'notice notice-success' : 'notice notice-error';
            const messageHtml = '<div class="' + messageClass + ' is-dismissible"><p>' + message + '</p></div>';

            $('.notice').remove();
            $('.info-container').prepend(messageHtml);
            if (type === 'success') {
                setTimeout(function() {
                    $('.notice-success').fadeOut();
                }, 3000);
            }
        }

    });



})(jQuery);