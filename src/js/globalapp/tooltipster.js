/*
Tooltipster.css

- text/inlineHtml: <div data-tooltip="html" | data-tooltip-theme="gold">
- externalHtml: <div data-toooltip-html><div data-tooltip-html-content>html-tip</div></div>
- image: <div data-tooltip-image="x">
- interactive: <div data-tooltip-interactive="tip">
*/

$.tooltipster.setDefaults({
  delay: 500,
  updateAnimation: false,
  maxWidth: 400,
  contentAsHTML: true,
})

$.fn.extend({
  updateTooltip(tooltip) {
    $(this).tooltipster('content', tooltip)
  },
})

function createFunctionInit(attribute) {
  return function FunctionInit(instance, helper) {
    instance.content(helper.origin.getAttribute(attribute))
  }
}

$('[data-tooltip]').tooltipster({
  functionInit: createFunctionInit('data-tooltip'),
})

$('[data-tooltip-theme="gold"]').tooltipster({
  functionInit: createFunctionInit('data-tooltip'),
  theme: '.tooltipster-default gold_theme',
})

$('[data-tooltip-image]').tooltipster({
  functionInit: createFunctionInit('data-tooltip-image'),
  fixedWidth: 252,
})

$('[data-tooltip-interactive]').tooltipster({
  functionInit: createFunctionInit('data-tooltip-interactive'),
  interactive: true,
  interactiveTolerance: 500,
})

$('[data-tooltip-html]').tooltipster({
  functionInit(instance, helper) {
    const content = helper.origin.querySelector('[data-tooltip-html-content]')
    content.remove()
    content.style.display = 'block'
    instance.content(content)
  },
})

$.tooltipster.group('grouped')
