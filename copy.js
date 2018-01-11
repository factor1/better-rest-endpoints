const fs = require('fs-extra'),
      target = '/Users/ericstout/Documents/projects/better-wp-endpoints-wp/app/public/wp-content/plugins/better-wp-endpoints',
      files = './';

fs.copy(files, target)
  .then(() => console.log('Files copied successfully!'))
  .catch(err => console.error(err))
