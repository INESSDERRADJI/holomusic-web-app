function setReply(id, author){
  document.getElementById('parent_id').value = id;
  document.getElementById('replyName').textContent = author;
  //on rend visible le reply to
  document.getElementById('replyInfo').classList.remove('d-none');
  document.getElementById('comment_text').focus();
}
function cancelReply(){
  document.getElementById('parent_id').value = '';
  document.getElementById('replyInfo').classList.add('d-none');
}
