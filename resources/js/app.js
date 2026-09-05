import katex from 'katex';
import 'katex/dist/katex.min.css';
import renderMathInElement from 'katex/dist/contrib/auto-render';
import { marked } from 'marked';

window.katex = katex;
window.renderMathInElement = renderMathInElement;
window.marked = marked;
