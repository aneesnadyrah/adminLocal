function getColorConfig(colorNames) {
const colorConfig = { colors: {}, colors_label: {} };

colorNames.forEach((colorName) => {
    const variableName = `--bs-${colorName}`;
    const labelVariableName = `--bs-${colorName}-text-emphasis`;

    colorConfig.colors[colorName] = window.getComputedStyle(document.documentElement).getPropertyValue(variableName);

    // Check if the label variable exists and has a value
    const labelValue = window.getComputedStyle(document.documentElement).getPropertyValue(labelVariableName);
    if (labelValue.trim() !== '') {
    colorConfig.colors_label[colorName] = labelValue;
    }
});

return colorConfig;
}

const colorNames = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'black', 'white', 'body-bg', 'text-dark', 'text-muted', 'border-color'];
const config = getColorConfig(colorNames);
