(function($) {
    'use strict';

    let isRunning = false;
    let testCount = 0;
    let totalTime = 0;
    let iterations = 0;

    function formatTime(time) {
        let seconds = time % 60;
        return `${Math.floor(time / 60).toString().padStart(2, '0')}:${Math.floor(seconds).toString().padStart(2, '0')}
			.${Math.floor(seconds % 1 * 1000).toString().padStart(3, '0')}`;
    }

    function getGrade(time) {
        const grades = [
            { time: 5, grade: 'A+', color: '#16a34a', desc: 'Exceptional' },
            { time: 10, grade: 'A', color: '#22c55e', desc: 'Excellent' },
            { time: 15, grade: 'B', color: '#84cc16', desc: 'Good' },
            { time: 20, grade: 'C', color: '#eab308', desc: 'Average' },
            { time: 25, grade: 'D', color: '#f97316', desc: 'Below Average' },
            { time: 30, grade: 'E', color: '#ef4444', desc: 'Poor' },
            { time: 40, grade: 'F', color: '#b91c1c', desc: 'Failed' }
        ];

        for (let grade of grades) {
            if (time <= grade.time) return grade;
        }
        return grades[grades.length - 1];
    }

    function createTest(data) {
        testCount++;

        let tbody = $('#testResultsTable tbody').length ? $('#testResultsTable tbody')[0] : null;
        if (!tbody) return;

        let row = tbody.insertRow();
        let nameCell = row.insertCell(0);
        nameCell.textContent = data.test_name;

        if (data.skipped) {
            let cell = row.insertCell(1);
            cell.textContent = data.skip_reason;
            cell.colSpan = 3;
            cell.style.color = '#64748b';
            cell.style.fontStyle = 'italic';
        } else {
            row.insertCell(1).textContent = data.time.toFixed(5) + 's';
            row.insertCell(2).textContent = Math.round(data.ops_per_ms) + ' op/ms';
            totalTime += data.time;
            iterations += data.iterations;
        }

    }

    function addTotalsRow() {
        let tfoot = $('#testResultsTable tfoot');
        if (!tfoot) return;

        const totalRow = $('<tr>');
        const grade = getGrade(totalTime);
        totalRow.css({
            'borderTop': '2px solid ' + grade.color,
        });
        totalRow.html(`
            <td>Totals</td>
            <td><strong>${totalTime.toFixed(5)}s</strong></td>
        `);
        tfoot.append(totalRow);

        let gradeContainer = $('.grade-container');
        let gradeDisplay = $('#gradeDisplay');
        let gradeDescription = $('#gradeDescription');

        if (gradeContainer && gradeDisplay && gradeDescription) {
            gradeContainer.show();
            gradeDisplay.text(grade.grade);
            gradeDisplay.css('color', grade.color);
            gradeDisplay.addClass('grade-circle');
            gradeDescription.text(grade.desc);
        }
    }

    function updateLoadingMessage(message) {
        const loading = $('#loading');
        if (loading) {
            loading.html(message);
        }
    }

    function runTest(testIndex = 0) {
        jQuery.ajax({
            url: phpvitals.ajaxurl,
            type: 'POST',
            data: {
                action: 'phpvitals_run_benchmark',
                nonce: phpvitals.nonce,
                test_index: testIndex
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;

                    updateTestProgress(data);
                    createTest(data);

                    if (testIndex < data.total_tests - 1) {
                        setTimeout(() => {
                            runTest(testIndex + 1, null);
                        }, 1000);
                    } else {
                        isRunning = false;
                        addTotalsRow();
                        jQuery('#loading').hide();
                        jQuery('#testResultsTable').show();

                        jQuery.ajax({
                            url: phpvitals.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'phpvitals_save_results',
                                nonce: phpvitals.nonce,
                                total_time: totalTime,
                                iterations: iterations,
                                test_count: testCount,
                                grade: getGrade(totalTime).grade
                            },
                            success: function(response) {
                                if (!response.success) {
                                    updateLoadingMessage('Failed to save benchmark results:', response.data.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                updateLoadingMessage('Error saving benchmark results:', error);
                            }
                        });
                    }
                } else {
                    updateLoadingMessage('Error running benchmark: ' + response.data.message);
                    isRunning = false;
                }
            },
            error: function(xhr, status, error) {
                updateLoadingMessage('Error running benchmark: ' + error);
                isRunning = false;
            }
        });
    }

    function updateTestProgress(data) {
        const loading = $('#loading');
        if (!loading) return;
        const message = `Running test ${data.test_index + 1}/${data.total_tests}:
			${data.test_name}`;

        updateLoadingMessage(message);
    }

    jQuery(document).ready(function($) {
        $('#terms-acceptance-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: phpvitals.ajaxurl,
                type: 'POST',
                data: {
                    action: 'phpvitals_handle_terms',
                    nonce: phpvitals.nonce,
                    terms_accept: $('#terms-accept').prop('checked') ? 'on' : 'off'
                },
                success: function(response) {
                    if (response.success && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        alert(response.data || 'Error accepting terms');
                    }
                },
                error: function() {
                    alert('Error accepting terms');
                }
            });
        });

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

    });



})(jQuery);