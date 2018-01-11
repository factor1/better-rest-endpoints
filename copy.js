const fs = require('fs-extra'),
      target = '/Users/ericstout/Documents/projects/better-rest-endpoints-wp/app/public/wp-content/plugins/better-rest-endpoints',
      files = './';

fs.copy(files, target)
  .then(() => console.log('Files copied successfully!'))
  .catch(err => console.error(err))
