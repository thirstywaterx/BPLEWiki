  // 函数用于解析URL中的查询字符串
  function hasQueryKey(key) {
    return new URLSearchParams(window.location.search).has(key);
  }

  // 检查查询字符串 'dev' 是否存在
  if (!hasQueryKey('dev')) {
    fetch('https://www.bplewiki.top/other/unopened.html')
        .then(response => response.text())
        .then(html => {
            document.open();
            document.write(html);
            document.close();
        })
        .catch(error => console.error('Error loading the page: ', error));
  } else {
    console.log('Dev mode activated, not replacing the document.');
  }