FROM node:16-alpine
RUN npm install -g markdown lint && npm install -g markdownlint-cli2 & npm install -g markdown-spellcheck
