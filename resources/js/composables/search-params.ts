export function useSearchParams() {
    
    function getParams(): URLSearchParams {
      return new URLSearchParams(window.location.search);
    }
  
    function updateURL(params: URLSearchParams) {
      const query = params.toString();
      const newURL = `${window.location.pathname}${query ? '?' + query : ''}`;
      history.replaceState({}, '', newURL);
    }
  
    function setParam(key: string, value: string) {
      const params = getParams();
      params.set(key, value);
      updateURL(params);
    }
  
    function removeParam(key: string) {
      const params = getParams();
      params.delete(key);
      updateURL(params);
    }
  
    function clearParams(pattern?: RegExp) {
      const params = getParams();
  
      for (const key of Array.from(params.keys())) {
        if (!pattern || pattern.test(key)) {
          params.delete(key);
        }
      }
  
      updateURL(params);
    }
  
    return {
      setParam,
      removeParam,
      clearParams,
    };
  }
  