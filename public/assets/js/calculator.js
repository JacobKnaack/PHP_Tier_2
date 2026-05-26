'use strict';

class Expression {
    constructor() {
        this.value = '';
        this.loading = false;
        this.loadingListeners = [];
    };
    setValue = (value) => {
        this.value = value;
    }
    append = (value) => {
        this.value += value;
    }
    addLoadingListener = (listener) => {
        this.loadingListeners.push(listener);
    }
    toggleLoading = (value) => {
        this.loading = value;
        this.loadingListeners.forEach((listener) => listener(this.loading));
    }
    evaluate = async (
        uri,
        method = 'POST'
    ) => {
        if (!validateExpression(this.value)) {
            throw new Error('Invalid expression');
        }
        this.toggleLoading(true);
        try {
            const params = new URLSearchParams();
            params.append('expression', this.value);
            const response = await fetch(uri, {
                method: method,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });
            const json = await response.json();
            return json.result;
        } catch (error) {
            this.setValue('');
            throw error;
        } finally {
            this.toggleLoading(false);
        }
    }
}

const expression = new Expression();
expression.addLoadingListener((isLoading) => {
    const loadingEl = document.getElementById('loading-progress');
    if (isLoading) {
        loadingEl.style.display = 'block';
    } else {
        loadingEl.style.display = 'none';
    }
});

function validateExpression(expression) {
    return /^[\d+\-x\/.]+$/.test(expression);
}

function getExpressionInput() {
    return document.getElementById('expression-input');
}
function getExpressionValues() {
    return document.getElementById('expression-values');
}
function getNumberButtons() {
    return document.querySelectorAll('.number-bttn');
}
function getOperationButtons() {
    return document.querySelectorAll('.operation-bttn');
}
function inputIncludesDecimal() {
    const inputEl = getExpressionInput();
    const lastNumber = inputEl.value.split(/[\+\-x\/]/).pop();
    if (lastNumber.includes('.')) {
        return true;
    }
}

function updateExpressionInput(value) {
    const input = getExpressionInput();
    input.value = value;
}
function clearExpressionInput() {
    updateExpressionInput('');
}
function updateExpressionValues(value) {
    const values = getExpressionValues();
    values.textContent = value;
}
function clearExpressionValues() {
    updateExpressionValues('');
}
function handleSubmit(e) {
    e.preventDefault();
    const inputEl = getExpressionInput();
    const valuesEl = getExpressionValues();
    expression.setValue(valuesEl.textContent + inputEl.value);
    expression.evaluate(e.target.action, e.target.method)
        .then((result) => {
            clearExpressionValues();
            updateExpressionInput(result);
        })
        .catch((error) => {
            updateExpressionValues('Error');
            clearExpressionInput();
            console.error(error);
        }); 
}

document.addEventListener('DOMContentLoaded', () => {
    const expressionInputEl = getExpressionInput();

    getNumberButtons().forEach((button) => {
        button.addEventListener('click', () => {
            if (button.value === '.' && inputIncludesDecimal()) {
                return;
            }
            updateExpressionInput(expressionInputEl.value + button.value);
        });
    });

    getOperationButtons().forEach((button) => {
        button.addEventListener('click', () => {
            const currentValue = expressionInputEl.value;
            if (currentValue === '') {
                return;
            }
            updateExpressionValues(currentValue + button.value);
            updateExpressionInput('');
        });
    });

    document.getElementById('clear-button').addEventListener('click', () => {
        clearExpressionInput();
        clearExpressionValues();
    });
    document.getElementById('calculator-form').addEventListener('submit', handleSubmit);
});

document.addEventListener('keydown', (e) => {
    const input = getExpressionInput();

    if (e.key >= '0' && e.key <= '9') {
        input.value += e.key;
        return;
    }

    if (e.key === '.') {
        // prevents multiple decimals
        if (!inputIncludesDecimal()) {
            input.value += '.';
        }
        return;
    }

    if (['+', '-', '*', '/'].includes(e.key)) {
        const currentValue = input.value;
        if (currentValue === '') {
            return;
        }
        updateExpressionValues(currentValue + e.key);
        updateExpressionInput('');
    }

    if (e.key === 'Backspace') {
        input.value = input.value.slice(0, -1);
        return;
    }

    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('equals-button').click();
    }
});
