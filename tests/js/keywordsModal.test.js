const fs = require('fs');
const path = require('path');

/**
 * @jest-environment jsdom
 */

import { initializeKeywordInput } from '../thesauri.js';

describe('initializeKeywordInput', () => {
  beforeEach(() => {
    document.body.innerHTML = fs.readFileSync(path.resolve(__dirname, '../../formgroups/thesaurusKeywords.html'), 'utf8');
    global.translations = require('../../lang/en.json');
  });
  test('initializes without throwing errors', (done) => {
    keywordConfigurations.forEach(config => {
        // Create the necessary HTML structure for each configuration
        initializeKeywordInput(config);
        setTimeout(() => {
          const input = document.querySelector(config.inputId);
          expect(input).toBeTruthy();
          expect(input._tagify).toBeDefined(); // Tagify instance was assigned
          done();
        }, 100);

        // Check if the modal was initialized correctly
        const modal = document.querySelector(config.modalId);
        expect(modal).toBeTruthy();

        // make an input for the jstree instance
        const jsTreeInput = document.getElementById(config.jsTreeId);
        // using geo as default test term
        jsTreeInput.value = 'geo';
        document.body.appendChild(jsTreeInput);
        // expect that keywords are found and displayed
        expect(document.querySelector('.jstree-search')).toBeTruthy();
        // if the expect is false, communicate the error
        if (!document.querySelector('.jstree-search')) {
          console.error(`Keywords not found for jsTree in modal ${config.modalId}`);
        }

        // make an input for the tagify instance
        const tagifyInput = document.getElementById(config.inputId);
        tagifyInput.value = 'geo';
        document.body.appendChild(tagifyInput);
        expect(document.querySelector('.tagify')).toBeTruthy();
        // if the expect is false, communicate the error
        if (!document.querySelector('.tagify')) {
            console.error(`Tagify not initialized for input ${config.inputId}`);
            }
      });
    }
    );