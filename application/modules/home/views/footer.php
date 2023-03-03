<script type="text/javascript">
	// Replace with your own values
const searchClient = algoliasearch(
  '5ZNG43LC3O',
  'eff3c1fc0e349d2c2f6e095ebb3b132b' // search only API key, not admin API key
);

const search = instantsearch({
  indexName: 'doubthelp_dev',
  searchClient,
  routing: true,
});

search.addWidgets([
  instantsearch.widgets.configure({
    hitsPerPage: 10,
  })
]);

search.addWidgets([
  instantsearch.widgets.searchBox({
    container: '#search-box',
    placeholder: 'Search for Questions',
  })
]);

search.addWidgets([
  instantsearch.widgets.hits({
    container: '#hits',
    templates: {
      item: document.getElementById('hit-template').innerHTML,
      empty: `We didn't find any results for the search <em>"{{query}}"</em>`,
    },
  })
]);

search.start();

</script>